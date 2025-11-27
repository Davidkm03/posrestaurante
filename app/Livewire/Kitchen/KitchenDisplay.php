<?php

namespace App\Livewire\Kitchen;

use App\Models\Order;
use App\Models\OrderItem;
use App\Enums\OrderStatus;
use Livewire\Component;

class KitchenDisplay extends Component
{
    public string $selectedStation = 'all';
    public array $stations = [];
    public bool $showCompletedOrders = false;
    public int $refreshInterval = 15; // seconds - más frecuente para cocina
    public string $viewMode = 'columns'; // 'columns' o 'grid'

    public function getListeners()
    {
        $branchId = session('current_branch_id', 1);
        
        return [
            'refresh-kitchen' => '$refresh',
            'refresh-orders' => '$refresh',
            "echo:kitchen.{$branchId},order.created" => 'orderCreated',
            "echo:kitchen.{$branchId},order.updated" => 'orderUpdated',
        ];
    }

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

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'columns' ? 'grid' : 'columns';
    }

    public function orderCreated($event)
    {
        $this->dispatch('notify', type: 'info', message: 'Nueva orden #' . ($event['order_number'] ?? ''));
        $this->dispatch('play-notification-sound');
    }

    public function orderUpdated($event)
    {
        // Refrescar silenciosamente
    }

    public function selectStation(string $station)
    {
        $this->selectedStation = $station;
    }

    public function startPreparation(int $orderId)
    {
        $order = Order::find($orderId);
        if (!$order) return;

        $order->update([
            'status' => OrderStatus::IN_PREPARATION->value,
            'preparation_started_at' => now(),
        ]);
        
        $this->dispatch('notify', type: 'info', message: 'Preparando orden #' . $order->order_number);
    }

    public function startItem(int $itemId)
    {
        $item = OrderItem::with('order')->find($itemId);
        if (!$item) return;

        // Actualiza el item
        $item->update(['kitchen_status' => 'preparing']);
        
        // Si la orden estaba pendiente, pasarla a preparación
        if ($item->order->status === OrderStatus::PENDING->value) {
            $item->order->update([
                'status' => OrderStatus::IN_PREPARATION->value,
                'preparation_started_at' => now(),
            ]);
        }
        
        $this->dispatch('notify', type: 'info', message: 'Preparando: ' . ($item->product_name ?? $item->product?->name));
    }

    public function completeItem(int $itemId)
    {
        $item = OrderItem::with('order')->find($itemId);
        if (!$item) return;

        $item->update([
            'kitchen_status' => 'ready',
            'is_ready' => true,
        ]);

        // Verificar si todos los items están listos
        $pendingItems = $item->order->items()
            ->where('kitchen_status', '!=', 'ready')
            ->where('is_ready', false)
            ->count();

        if ($pendingItems === 0) {
            $item->order->update([
                'status' => OrderStatus::READY->value,
                'ready_at' => now(),
            ]);
            $this->dispatch('notify', type: 'success', message: 'Orden #' . $item->order->order_number . ' completa');
            $this->dispatch('play-notification-sound');
        } else {
            $this->dispatch('notify', type: 'success', message: 'Item listo: ' . ($item->product_name ?? $item->product?->name));
        }
    }

    public function completeOrder(int $orderId)
    {
        $order = Order::find($orderId);
        if (!$order) return;

        $order->update([
            'status' => OrderStatus::READY->value,
            'ready_at' => now(),
        ]);

        // Marcar todos los items como listos
        $order->items()->update([
            'kitchen_status' => 'ready',
            'is_ready' => true,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Orden #' . $order->order_number . ' lista para servir');
        $this->dispatch('play-notification-sound');
    }

    public function bumpOrder(int $orderId)
    {
        $order = Order::find($orderId);
        if (!$order) return;

        $order->update([
            'status' => OrderStatus::DELIVERED->value,
            'served_at' => now(),
        ]);

        $order->items()->update(['kitchen_status' => 'served']);

        $this->dispatch('notify', type: 'success', message: 'Orden #' . $order->order_number . ' despachada');
    }

    public function recallOrder(int $orderId)
    {
        $order = Order::find($orderId);
        if (!$order) return;

        $order->update([
            'status' => OrderStatus::IN_PREPARATION->value,
            'ready_at' => null,
        ]);

        $this->dispatch('notify', type: 'info', message: 'Orden #' . $order->order_number . ' regresada a cocina');
    }

    public function getOrders()
    {
        $branchId = session('current_branch_id');
        
        $statuses = [
            OrderStatus::PENDING->value,
            OrderStatus::IN_PREPARATION->value,
            OrderStatus::READY->value,
        ];

        $query = Order::with(['items.product.comboProducts', 'items.modifiers', 'table', 'waiter'])
            ->where('branch_id', $branchId)
            ->whereIn('status', $statuses)
            ->orderBy('created_at', 'asc');

        if ($this->showCompletedOrders) {
            $query->orWhere(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->where('status', OrderStatus::DELIVERED->value)
                  ->where('updated_at', '>=', now()->subHours(2));
            });
        }

        return $query->get();
    }

    public function getElapsedTime($createdAt): array
    {
        if (!$createdAt) return ['minutes' => 0, 'seconds' => 0, 'urgent' => false, 'warning' => false];

        $created = \Carbon\Carbon::parse($createdAt);
        $diff = now()->diff($created);

        $totalMinutes = ($diff->h * 60) + $diff->i;

        return [
            'minutes' => $totalMinutes,
            'seconds' => $diff->s,
            'urgent' => $totalMinutes >= 20,
            'warning' => $totalMinutes >= 10 && $totalMinutes < 20,
        ];
    }

    public function getStats(): array
    {
        $branchId = session('current_branch_id');
        
        $pending = Order::where('branch_id', $branchId)
            ->where('status', OrderStatus::PENDING->value)
            ->count();
            
        $preparing = Order::where('branch_id', $branchId)
            ->where('status', OrderStatus::IN_PREPARATION->value)
            ->count();
            
        $ready = Order::where('branch_id', $branchId)
            ->where('status', OrderStatus::READY->value)
            ->count();

        // Tiempo promedio de preparación hoy
        $avgTime = Order::where('branch_id', $branchId)
            ->whereNotNull('preparation_started_at')
            ->whereNotNull('ready_at')
            ->whereDate('created_at', today())
            ->selectRaw('AVG(ROUND((JULIANDAY(ready_at) - JULIANDAY(preparation_started_at)) * 24 * 60)) as avg_minutes')
            ->value('avg_minutes');

        return [
            'pending' => $pending,
            'preparing' => $preparing,
            'ready' => $ready,
            'avg_time' => (int) ($avgTime ?? 0),
        ];
    }

    public function render()
    {
        $orders = $this->getOrders();
        $stats = $this->getStats();

        // Group orders by status - comparar con Enum directamente
        $pendingOrders = $orders->filter(fn($o) => $o->status === OrderStatus::PENDING);
        $preparingOrders = $orders->filter(fn($o) => $o->status === OrderStatus::IN_PREPARATION);
        $readyOrders = $orders->filter(fn($o) => $o->status === OrderStatus::READY);

        return view('livewire.kitchen.kitchen-display', [
            'pendingOrders' => $pendingOrders,
            'preparingOrders' => $preparingOrders,
            'readyOrders' => $readyOrders,
            'stats' => $stats,
            'totalOrders' => $orders->count(),
        ]);
    }
}
