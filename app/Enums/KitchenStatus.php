<?php

namespace App\Enums;

enum KitchenStatus: string
{
    case PENDING = 'pending';
    case PREPARING = 'preparing';
    case READY = 'ready';
    case SERVED = 'served';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendiente',
            self::PREPARING => 'Preparando',
            self::READY => 'Listo',
            self::SERVED => 'Servido',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::PREPARING => 'blue',
            self::READY => 'green',
            self::SERVED => 'gray',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::PENDING => 'clock',
            self::PREPARING => 'fire',
            self::READY => 'check-circle',
            self::SERVED => 'check-badge',
        };
    }
}
