<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Collection;

class NotificationCenter extends Component
{
    public array $notifications = [];
    public int $unreadCount = 0;
    public bool $isOpen = false;

    public function mount()
    {
        // Cargar notificaciones persistentes del usuario si hay
        $this->loadStoredNotifications();
    }

    protected function loadStoredNotifications()
    {
        // Por ahora las notificaciones son solo en memoria
        // Aquí podrías cargar de la base de datos
    }

    #[On('echo:kitchen.{branchId},order.created')]
    public function handleNewOrder($data)
    {
        $this->addNotification([
            'type' => 'order',
            'title' => 'Nueva Orden #' . $data['order_number'],
            'message' => 'Mesa: ' . ($data['table'] ?? 'N/A') . ' - $' . number_format($data['total'], 0, ',', '.'),
            'icon' => 'bell',
            'color' => 'blue',
            'link' => route('kitchen.index'),
            'timestamp' => now(),
        ]);
    }

    #[On('echo:pos.{branchId},item.ready')]
    public function handleItemReady($data)
    {
        $this->addNotification([
            'type' => 'kitchen',
            'title' => 'Plato Listo',
            'message' => $data['quantity'] . 'x ' . $data['product_name'] . ' - Mesa ' . $data['table'],
            'icon' => 'check-circle',
            'color' => 'green',
            'link' => null,
            'timestamp' => now(),
        ]);
    }

    #[On('echo:inventory.{branchId},stock.low')]
    public function handleLowStock($data)
    {
        $alertType = $data['alert_type'] === 'out_of_stock' ? 'Sin Stock' : 'Stock Bajo';
        
        $this->addNotification([
            'type' => 'inventory',
            'title' => $alertType . ': ' . $data['product_name'],
            'message' => 'Stock actual: ' . $data['current_stock'] . ' (mínimo: ' . $data['min_stock'] . ')',
            'icon' => 'exclamation-triangle',
            'color' => $data['alert_type'] === 'out_of_stock' ? 'red' : 'yellow',
            'link' => route('admin.inventory.show', $data['product_id']),
            'timestamp' => now(),
        ]);
    }

    #[On('echo:pos.{branchId},order.updated')]
    public function handleOrderUpdate($data)
    {
        if ($data['status'] === 'cancelled') {
            $this->addNotification([
                'type' => 'warning',
                'title' => 'Orden Cancelada',
                'message' => 'Orden #' . $data['order_number'] . ' ha sido cancelada',
                'icon' => 'x-circle',
                'color' => 'red',
                'link' => null,
                'timestamp' => now(),
            ]);
        }
    }

    public function addNotification(array $notification)
    {
        $notification['id'] = uniqid('notif_');
        $notification['read'] = false;
        
        // Añadir al inicio del array
        array_unshift($this->notifications, $notification);
        
        // Mantener solo las últimas 50 notificaciones
        if (count($this->notifications) > 50) {
            $this->notifications = array_slice($this->notifications, 0, 50);
        }
        
        $this->unreadCount++;
        
        // Emitir evento para mostrar toast
        $this->dispatch('notify', 
            type: $notification['color'] ?? 'info',
            message: $notification['title'] . ': ' . $notification['message']
        );
    }

    public function togglePanel()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function markAsRead(string $id)
    {
        foreach ($this->notifications as &$notification) {
            if ($notification['id'] === $id && !$notification['read']) {
                $notification['read'] = true;
                $this->unreadCount = max(0, $this->unreadCount - 1);
                break;
            }
        }
    }

    public function markAllAsRead()
    {
        foreach ($this->notifications as &$notification) {
            $notification['read'] = true;
        }
        $this->unreadCount = 0;
    }

    public function clearAll()
    {
        $this->notifications = [];
        $this->unreadCount = 0;
    }

    public function removeNotification(string $id)
    {
        $this->notifications = array_values(
            array_filter($this->notifications, function($n) use ($id) {
                if ($n['id'] === $id && !$n['read']) {
                    $this->unreadCount = max(0, $this->unreadCount - 1);
                }
                return $n['id'] !== $id;
            })
        );
    }

    public function render()
    {
        return view('livewire.notification-center');
    }
}
