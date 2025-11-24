<?php

namespace App\Livewire\POS;

use App\Models\Product;
use App\Models\Modifier;
use App\Services\OrderService;
use App\Enums\OrderType;
use Livewire\Component;
use Livewire\Attributes\On;

class Cart extends Component
{
    public ?int $tableId = null;
    public ?int $customerId = null;
    public ?int $orderId = null;
    public string $orderType = 'dine_in';
    public array $items = [];
    public float $subtotal = 0;
    public float $tax = 0;
    public float $discount = 0;
    public float $total = 0;
    public string $discountType = 'percentage';
    public float $discountValue = 0;
    public string $discountReason = '';
    public string $notes = '';
    public int $guests = 1;

    public function mount(?int $tableId = null, ?int $orderId = null)
    {
        $this->tableId = $tableId;
        $this->orderId = $orderId;

        if ($orderId) {
            $this->loadOrder($orderId);
        }
    }

    #[On('add-product')]
    public function addProduct(int $productId, array $modifiers = [], string $notes = '')
    {
        $product = Product::find($productId);

        if (!$product) return;

        $modifierDetails = [];
        $modifiersTotal = 0;

        if (!empty($modifiers)) {
            $modifierModels = Modifier::whereIn('id', $modifiers)->get();
            foreach ($modifierModels as $mod) {
                $modifierDetails[] = [
                    'id' => $mod->id,
                    'name' => $mod->name,
                    'price' => $mod->price,
                ];
                $modifiersTotal += $mod->price;
            }
        }

        $unitPrice = $product->price + $modifiersTotal;

        // Check if same product with same modifiers exists
        $existingIndex = $this->findExistingItem($productId, $modifiers);

        if ($existingIndex !== null && empty($notes)) {
            $this->items[$existingIndex]['quantity']++;
            $this->items[$existingIndex]['subtotal'] = $this->items[$existingIndex]['quantity'] * $this->items[$existingIndex]['unit_price'];
        } else {
            $this->items[] = [
                'id' => uniqid(),
                'product_id' => $product->id,
                'name' => $product->name,
                'unit_price' => $unitPrice,
                'base_price' => $product->price,
                'quantity' => 1,
                'subtotal' => $unitPrice,
                'modifiers' => $modifierDetails,
                'notes' => $notes,
                'tax_type' => $product->tax_type,
                'tax_percentage' => $product->tax_percentage,
            ];
        }

        $this->recalculate();
    }

    public function updateQuantity(string $itemId, int $quantity)
    {
        $index = $this->findItemIndex($itemId);

        if ($index === null) return;

        if ($quantity <= 0) {
            $this->removeItem($itemId);
            return;
        }

        $this->items[$index]['quantity'] = $quantity;
        $this->items[$index]['subtotal'] = $quantity * $this->items[$index]['unit_price'];

        $this->recalculate();
    }

    public function incrementQuantity(string $itemId)
    {
        $index = $this->findItemIndex($itemId);
        if ($index !== null) {
            $this->updateQuantity($itemId, $this->items[$index]['quantity'] + 1);
        }
    }

    public function decrementQuantity(string $itemId)
    {
        $index = $this->findItemIndex($itemId);
        if ($index !== null) {
            $this->updateQuantity($itemId, $this->items[$index]['quantity'] - 1);
        }
    }

    public function removeItem(string $itemId)
    {
        $this->items = array_values(array_filter($this->items, fn($item) => $item['id'] !== $itemId));
        $this->recalculate();
    }

    public function clearCart()
    {
        $this->items = [];
        $this->discount = 0;
        $this->discountValue = 0;
        $this->notes = '';
        $this->recalculate();
    }

    public function applyDiscount()
    {
        if ($this->discountValue <= 0) {
            $this->discount = 0;
            $this->recalculate();
            return;
        }

        if ($this->discountType === 'percentage') {
            $this->discount = $this->subtotal * ($this->discountValue / 100);
        } else {
            $this->discount = min($this->discountValue, $this->subtotal);
        }

        $this->recalculate();
    }

    public function removeDiscount()
    {
        $this->discount = 0;
        $this->discountValue = 0;
        $this->discountReason = '';
        $this->recalculate();
    }

    public function createOrder()
    {
        if (empty($this->items)) {
            $this->dispatch('notify', type: 'error', message: 'Agregue productos al carrito');
            return;
        }

        $orderService = app(OrderService::class);

        try {
            $orderItems = array_map(fn($item) => [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'notes' => $item['notes'],
                'modifiers' => array_column($item['modifiers'], 'id'),
            ], $this->items);

            $order = $orderService->create([
                'branch_id' => session('current_branch_id'),
                'cash_session_id' => session('cash_session_id'),
                'type' => $this->orderType,
                'table_id' => $this->tableId,
                'customer_id' => $this->customerId,
                'items' => $orderItems,
                'notes' => $this->notes,
                'guests' => $this->guests,
            ]);

            if ($this->discount > 0) {
                $orderService->applyDiscount($order, $this->discountType, $this->discountValue, $this->discountReason);
            }

            $this->orderId = $order->id;
            $this->dispatch('order-created', orderId: $order->id);
            $this->dispatch('notify', type: 'success', message: 'Orden creada: ' . $order->order_number);

        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function sendToKitchen()
    {
        if (!$this->orderId) {
            $this->createOrder();
        }

        if ($this->orderId) {
            $orderService = app(OrderService::class);
            $order = \App\Models\Order::find($this->orderId);
            $orderService->sendToKitchen($order);

            $this->dispatch('notify', type: 'success', message: 'Enviado a cocina');
            $this->dispatch('order-sent-to-kitchen', orderId: $this->orderId);
        }
    }

    public function goToPayment()
    {
        if (!$this->orderId) {
            $this->createOrder();
        }

        if ($this->orderId) {
            $this->dispatch('open-payment-modal', orderId: $this->orderId);
        }
    }

    protected function recalculate()
    {
        $this->subtotal = array_sum(array_column($this->items, 'subtotal'));

        $this->tax = 0;
        foreach ($this->items as $item) {
            $itemTax = ($item['subtotal'] * $item['tax_percentage']) / 100;
            $this->tax += $itemTax;
        }

        $this->total = $this->subtotal - $this->discount;
    }

    protected function findExistingItem(int $productId, array $modifiers): ?int
    {
        foreach ($this->items as $index => $item) {
            if ($item['product_id'] === $productId) {
                $existingModifiers = array_column($item['modifiers'], 'id');
                sort($existingModifiers);
                sort($modifiers);

                if ($existingModifiers === $modifiers && empty($item['notes'])) {
                    return $index;
                }
            }
        }
        return null;
    }

    protected function findItemIndex(string $itemId): ?int
    {
        foreach ($this->items as $index => $item) {
            if ($item['id'] === $itemId) {
                return $index;
            }
        }
        return null;
    }

    protected function loadOrder(int $orderId)
    {
        $order = \App\Models\Order::with(['items.modifiers', 'customer'])->find($orderId);

        if (!$order) return;

        $this->orderType = $order->type;
        $this->customerId = $order->customer_id;
        $this->notes = $order->notes ?? '';
        $this->guests = $order->guests ?? 1;

        foreach ($order->items as $item) {
            $modifierDetails = $item->modifiers->map(fn($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'price' => $m->price,
            ])->toArray();

            $this->items[] = [
                'id' => uniqid(),
                'product_id' => $item->product_id,
                'name' => $item->product_name,
                'unit_price' => $item->unit_price,
                'base_price' => $item->unit_price - array_sum(array_column($modifierDetails, 'price')),
                'quantity' => $item->quantity,
                'subtotal' => $item->subtotal,
                'modifiers' => $modifierDetails,
                'notes' => $item->notes ?? '',
                'tax_type' => $item->tax_type,
                'tax_percentage' => $item->tax_percentage,
            ];
        }

        $this->discount = $order->discount;
        $this->recalculate();
    }

    public function render()
    {
        return view('livewire.pos.cart');
    }
}
