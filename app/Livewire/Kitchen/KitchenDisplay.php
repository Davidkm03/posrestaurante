<?php

namespace App\Livewire\Kitchen;

use App\Models\Order;
use App\Models\OrderItem;
use App\Enums\OrderStatus;
use App\Enums\KitchenStatus;
use Livewire\Component;

class KitchenDisplay extends Component
{
    public string $selectedStation = 'all';
    public array $stations = [];
    public bool $showCompletedOrders = false;
    public int $refreshInterval = 30; // seconds

    protected $listeners = ['refresh-kitchen' => '$refresh'];

    public function mount()
    {
        $this->stations = [
            'all' => 'Todas',
            'cocina' => 'Cocina',
            'parrilla' => 'Parrilla',
            'bar' => 'Bar',
            'postres' => 'Postres',
        ];
    }

    public function selectStation(string $station)
    {
        $this->selectedStation = $station;
    }

    public function startItem(int $itemId)
    {
        $item = OrderItem::find($itemId);
        if (!$item) return;

        $item->update([
            'kitchen_status' => KitchenStatus::PREPARING->value,
            'started_at' => now(),
        ]);

        $this->updateOrderStatus($item->order_id);
        $this->dispatch('notify', type: 'info', message: 'Preparando: ' . $item->product_name);
    }

    public function completeItem(int $itemId)
    {
        $item = OrderItem::find($itemId);
        if (!$item) return;

        $item->update([
            'kitchen_status' => KitchenStatus::READY->value,
            'completed_at' => now(),
        ]);

        $this->updateOrderStatus($item->order_id);
        $this->dispatch('notify', type: 'success', message: 'Listo: ' . $item->product_name);
    }

    public function completeOrder(int $orderId)
    {
        $order = Order::find($orderId);
        if (!$order) return;

        // Mark all items as ready
        $order->items()->update([
            'kitchen_status' => KitchenStatus::READY->value,
            'completed_at' => now(),
        ]);

        $order->update([
            'status' => OrderStatus::READY->value,
            'ready_at' => now(),
        ]);

        $this->dispatch('notify', type: 'success', message: 'Orden ' . $order->order_number . ' lista para servir');
    }

    public function bumpOrder(int $orderId)
    {
        $order = Order::find($orderId);
        if (!$order) return;

        $order->update([
            'status' => OrderStatus::SERVED->value,
            'served_at' => now(),
        ]);

        $this->dispatch('notify', type: 'success', message: 'Orden ' . $order->order_number . ' despachada');
    }

    public function recallOrder(int $orderId)
    {
        $order = Order::find($orderId);
        if (!$order) return;

        $order->update([
            'status' => OrderStatus::IN_KITCHEN->value,
            'ready_at' => null,
        ]);

        $this->dispatch('notify', type: 'info', message: 'Orden ' . $order->order_number . ' regresada a cocina');
    }

    protected function updateOrderStatus(int $orderId)
    {
        $order = Order::with('items')->find($orderId);
        if (!$order) return;

        $allReady = $order->items->every(fn($item) => $item->kitchen_status === KitchenStatus::READY->value);
        $anyPreparing = $order->items->contains(fn($item) => $item->kitchen_status === KitchenStatus::PREPARING->value);

        if ($allReady) {
            $order->update([
                'status' => OrderStatus::READY->value,
                'ready_at' => now(),
            ]);
        } elseif ($anyPreparing && $order->status === OrderStatus::PENDING->value) {
            $order->update(['status' => OrderStatus::IN_KITCHEN->value]);
        }
    }

    public function getOrders()
    {
        $query = Order::with(['items.product', 'table', 'waiter'])
            ->whereIn('status', [
                OrderStatus::PENDING->value,
                OrderStatus::IN_KITCHEN->value,
                OrderStatus::READY->value,
            ])
            ->where('sent_to_kitchen', true);

        if ($this->showCompletedOrders) {
            $query->orWhere(function ($q) {
                $q->where('status', OrderStatus::SERVED->value)
                  ->where('served_at', '>=', now()->subHours(2));
            });
        }

        return $query->orderBy('sent_to_kitchen_at')->get();
    }

    public function getElapsedTime($sentAt): array
    {
        if (!$sentAt) return ['minutes' => 0, 'seconds' => 0, 'urgent' => false];

        $sent = \Carbon\Carbon::parse($sentAt);
        $diff = now()->diff($sent);

        $totalMinutes = ($diff->h * 60) + $diff->i;

        return [
            'minutes' => $totalMinutes,
            'seconds' => $diff->s,
            'urgent' => $totalMinutes >= 15,
            'warning' => $totalMinutes >= 10 && $totalMinutes < 15,
        ];
    }

    public function render()
    {
        $orders = $this->getOrders();

        // Group orders by status
        $pendingOrders = $orders->filter(fn($o) => $o->status === OrderStatus::PENDING->value);
        $preparingOrders = $orders->filter(fn($o) => $o->status === OrderStatus::IN_KITCHEN->value);
        $readyOrders = $orders->filter(fn($o) => $o->status === OrderStatus::READY->value);

        return view('livewire.kitchen.kitchen-display', [
            'pendingOrders' => $pendingOrders,
            'preparingOrders' => $preparingOrders,
            'readyOrders' => $readyOrders,
            'totalOrders' => $orders->count(),
        ]);
    }
}
