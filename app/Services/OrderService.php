<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Table;
use App\Models\Modifier;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\TableStatus;
use App\Events\OrderCreated;
use App\Events\OrderStatusUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class OrderService
{
    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = Order::create([
                'branch_id' => $data['branch_id'],
                'cash_session_id' => $data['cash_session_id'],
                'order_number' => $this->generateOrderNumber($data['branch_id']),
                'type' => $data['type'],
                'table_id' => $data['table_id'] ?? null,
                'customer_id' => $data['customer_id'] ?? null,
                'user_id' => $data['user_id'] ?? auth()->id(),
                'status' => OrderStatus::PENDING->value,
                'guests' => $data['guests'] ?? 1,
                'notes' => $data['notes'] ?? null,
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total' => 0,
            ]);

            $this->addItems($order, $data['items']);

            if ($order->table_id) {
                $this->updateTableStatus($order->table_id, TableStatus::OCCUPIED, $order->id);
            }

            $order = $order->fresh(['items.product', 'items.modifiers', 'table', 'customer']);
            
            // Broadcast evento de nueva orden
            event(new OrderCreated($order));
            
            return $order;
        });
    }

    public function addItems(Order $order, array $items): Order
    {
        foreach ($items as $itemData) {
            $this->addItem($order, $itemData);
        }

        return $this->recalculateTotals($order);
    }

    public function addItem(Order $order, array $itemData): OrderItem
    {
        $product = Product::findOrFail($itemData['product_id']);

        $unitPrice = $product->price;
        $modifiersData = [];

        if (!empty($itemData['modifiers'])) {
            $modifiers = Modifier::whereIn('id', $itemData['modifiers'])->get();
            foreach ($modifiers as $modifier) {
                $modifiersData[] = [
                    'modifier_id' => $modifier->id,
                    'name' => $modifier->name,
                    'price' => $modifier->price,
                    'quantity' => 1,
                ];
                $unitPrice += $modifier->price;
            }
        }

        $quantity = $itemData['quantity'];
        $subtotal = $unitPrice * $quantity;

        $taxAmount = $this->calculateTax($subtotal, $product->tax_percentage ?? 0, $product->tax_included ?? false);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'tax_amount' => $taxAmount,
            'total' => $subtotal,
            'notes' => $itemData['notes'] ?? null,
            'status' => 'pending',
        ]);

        // Create modifier records
        foreach ($modifiersData as $modifierData) {
            $orderItem->modifiers()->create($modifierData);
        }

        return $orderItem;
    }

    public function removeItem(Order $order, OrderItem $item): Order
    {
        $item->delete();

        if ($order->items()->count() === 0) {
            return $this->cancel($order, 'Todos los items eliminados');
        }

        return $this->recalculateTotals($order);
    }

    public function updateItemQuantity(Order $order, OrderItem $item, int $quantity): Order
    {
        $item->update([
            'quantity' => $quantity,
            'subtotal' => $item->unit_price * $quantity,
            'tax_amount' => $this->calculateTax(
                $item->unit_price * $quantity,
                $item->tax_percentage,
                true
            ),
        ]);

        return $this->recalculateTotals($order);
    }

    public function applyDiscount(Order $order, string $type, float $value, ?string $reason = null): Order
    {
        $discount = match ($type) {
            'percentage' => $order->subtotal * ($value / 100),
            'fixed' => min($value, $order->subtotal),
            default => 0,
        };

        $order->update([
            'discount_amount' => $discount,
            'discount_type' => $type,
            'discount_reason' => $reason,
            'discount_authorized_by' => auth()->id(),
            'total' => $order->subtotal - $discount,
        ]);

        return $order->fresh();
    }

    public function sendToKitchen(Order $order): Order
    {
        $oldStatus = $order->status->value;
        
        $order->update([
            'status' => OrderStatus::IN_PREPARATION->value,
        ]);

        $order->items()
            ->where('status', 'pending')
            ->update(['status' => 'preparing']);

        $order = $order->fresh();
        event(new OrderStatusUpdated($order, $oldStatus));
        
        return $order;
    }

    public function markReady(Order $order): Order
    {
        $oldStatus = $order->status->value;
        
        $order->update([
            'status' => OrderStatus::READY->value,
        ]);

        $order->items()->update([
            'status' => 'ready',
            'prepared_at' => now(),
        ]);

        $order = $order->fresh();
        event(new OrderStatusUpdated($order, $oldStatus));
        
        return $order;
    }

    public function markDelivered(Order $order): Order
    {
        $order->update([
            'status' => OrderStatus::DELIVERED->value,
        ]);

        $order->items()->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        return $order->fresh();
    }

    public function cancel(Order $order, string $reason): Order
    {
        $order->update([
            'status' => OrderStatus::CANCELLED->value,
            'notes' => ($order->notes ? $order->notes . "\n" : '') . "Cancelado: " . $reason,
        ]);

        if ($order->table_id) {
            $this->updateTableStatus($order->table_id, TableStatus::FREE);
        }

        return $order->fresh();
    }

    public function complete(Order $order): Order
    {
        $order->update([
            'status' => OrderStatus::PAID->value,
            'payment_status' => \App\Enums\PaymentStatus::COMPLETED->value,
            'completed_at' => now(),
        ]);

        if ($order->table_id) {
            $this->updateTableStatus($order->table_id, TableStatus::FREE);
        }

        return $order->fresh();
    }

    public function recalculateTotals(Order $order): Order
    {
        $items = $order->items()->get();

        $subtotal = $items->sum('total');
        $tax = $items->sum('tax_amount');
        $discount = $order->discount_amount ?? 0;

        $order->update([
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total' => $subtotal - $discount,
        ]);

        return $order->fresh();
    }

    public function generateOrderNumber(int $branchId): string
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');

        $lastOrder = Order::where('branch_id', $branchId)
            ->whereDate('created_at', today())
            ->orderByDesc('id')
            ->first();

        $sequence = $lastOrder
            ? intval(substr($lastOrder->order_number, -4)) + 1
            : 1;

        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    protected function calculateTax(float $amount, float $percentage, bool $included): float
    {
        if ($included) {
            return $amount - ($amount / (1 + $percentage / 100));
        }

        return $amount * ($percentage / 100);
    }

    protected function updateTableStatus(int $tableId, TableStatus $status, ?int $orderId = null): void
    {
        Table::where('id', $tableId)->update([
            'status' => $status->value,
            'current_order_id' => $orderId,
        ]);
    }

    public function getDailySummary(int $branchId, ?string $date = null): array
    {
        $date = $date ? \Carbon\Carbon::parse($date) : today();

        $orders = Order::where('branch_id', $branchId)
            ->whereDate('created_at', $date)
            ->get();

        $validOrders = $orders->whereNotIn('status', [
            OrderStatus::CANCELLED->value,
        ]);

        return [
            'total_orders' => $orders->count(),
            'completed_orders' => $validOrders->count(),
            'cancelled_orders' => $orders->where('status', OrderStatus::CANCELLED->value)->count(),
            'total_sales' => $validOrders->sum('total'),
            'total_tax' => $validOrders->sum('tax_amount'),
            'total_discount' => $validOrders->sum('discount_amount'),
            'avg_ticket' => $validOrders->count() > 0 ? $validOrders->avg('total') : 0,
            'by_type' => $validOrders->groupBy('type')->map->count(),
            'by_status' => $orders->groupBy('status')->map->count(),
        ];
    }
}
