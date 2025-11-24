<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class NotificationToast extends Component
{
    public array $notifications = [];

    #[On('notify')]
    public function addNotification(string $type, string $message, int $duration = 5000)
    {
        $id = uniqid();

        $this->notifications[] = [
            'id' => $id,
            'type' => $type,
            'message' => $message,
            'duration' => $duration,
        ];

        // Auto remove after duration (handled by Alpine.js)
    }

    public function removeNotification(string $id)
    {
        $this->notifications = array_values(
            array_filter($this->notifications, fn($n) => $n['id'] !== $id)
        );
    }

    public function render()
    {
        return view('livewire.notification-toast');
    }
}
