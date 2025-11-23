<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';
    case PARTIAL = 'partial';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendiente',
            self::COMPLETED => 'Completado',
            self::FAILED => 'Fallido',
            self::REFUNDED => 'Reembolsado',
            self::PARTIAL => 'Parcial',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::COMPLETED => 'green',
            self::FAILED => 'red',
            self::REFUNDED => 'purple',
            self::PARTIAL => 'orange',
        };
    }
}
