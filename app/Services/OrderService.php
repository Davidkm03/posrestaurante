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
                'waiter_id' => $data['waiter_id'] ?? auth()->id(),
                'status' => OrderStatus::PENDING->value,
                'guests' => $data['guests'] ?? 1,
                'notes' => $data['notes'] ?? null,
                'subtotal' => 0,
                'tax' => 0,
                'discount' => 0,
                'total' => 0,
            ]);

            $this->addItems($order, $data['items']);

            if ($order->table_id) {
                $this->updateTableStatus($order->table_id, TableStatus::OCCUPIED, $order->id);
            }

            return $order->fresh(['items.product', 'items.modifiers', 'table', 'customer']);
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
        $modifiersTotal = 0;
        $modifierIds = [];

        if (!empty($itemData['modifiers'])) {
            $modifiers = Modifier::whereIn('id', $itemData['modifiers'])->get();
            $modifiersTotal = $modifiers->sum('price');
            $modifierIds = $modifiers->pluck('id')->toArray();
        }

        $unitPrice += $modifiersTotal;
        $quantity = $itemData['quantity'];
        $subtotal = $unitPrice * $quantity;

        $taxAmount = $this->calculateTax($subtotal, $product->tax_percentage, $product->tax_included);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
            'tax_type' => $product->tax_type,
            'tax_percentage' => $product->tax_percentage,
            'tax_amount' => $taxAmount,
            'notes' => $itemData['notes'] ?? null,
            'status' => 'pending',
        ]);

        if (!empty($modifierIds)) {
            $orderItem->modifiers()->attach($modifierIds);
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
            'discount' => $discount,
            'discount_type' => $type,
            'discount_value' => $value,
            'discount_reason' => $reason,
            'discount_by' => auth()->id(),
            'total' => $order->subtotal - $discount,
        ]);

        return $order->fresh();
    }

    public function sendToKitchen(Order $order): Order
    {
        $order->update([
            'status' => OrderStatus::IN_PREPARATION->value,
            'sent_to_kitchen_at' => now(),
        ]);

        $order->items()
            ->where('status', 'pending')
            ->update(['status' => 'preparing']);

        return $order->fresh();
    }

    public function markReady(Order $order): Order
    {
        $order->update([
            'status' => OrderStatus::READY->value,
            'ready_at' => now(),
        ]);

        $order->items()->update([
            'status' => 'ready',
            'prepared_at' => now(),
        ]);

        return $order->fresh();
    }

    public function markDelivered(Order $order): Order
    {
        $order->update([
            'status' => OrderStatus::DELIVERED->value,
            'delivered_at' => now(),
        ]);

        $order->items()->update(['status' => 'delivered']);

        return $order->fresh();
    }

    public function cancel(Order $order, string $reason): Order
    {
        $order->update([
            'status' => OrderStatus::CANCELLED->value,
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id(),
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
            'paid_at' => now(),
            'cashier_id' => auth()->id(),
        ]);

        if ($order->table_id) {
            $this->updateTableStatus($order->table_id, TableStatus::FREE);
        }

        return $order->fresh();
    }

    public function recalculateTotals(Order $order): Order
    {
        $items = $order->items()->get();

        $subtotal = $items->sum('subtotal');
        $tax = $items->sum('tax_amount');
        $discount = $order->discount ?? 0;

        $order->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
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
            OrderStatus::VOIDED->value,
        ]);

        return [
            'total_orders' => $orders->count(),
            'completed_orders' => $validOrders->count(),
            'cancelled_orders' => $orders->where('status', OrderStatus::CANCELLED->value)->count(),
            'total_sales' => $validOrders->sum('total'),
            'total_tax' => $validOrders->sum('tax'),
            'total_discount' => $validOrders->sum('discount'),
            'avg_ticket' => $validOrders->count() > 0 ? $validOrders->avg('total') : 0,
            'by_type' => $validOrders->groupBy('type')->map->count(),
            'by_status' => $orders->groupBy('status')->map->count(),
        ];
    }
}
