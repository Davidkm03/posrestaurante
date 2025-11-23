<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case IN_PREPARATION = 'in_preparation';
    case READY = 'ready';
    case DELIVERED = 'delivered';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendiente',
            self::IN_PREPARATION => 'En Preparación',
            self::READY => 'Listo',
            self::DELIVERED => 'Entregado',
            self::PAID => 'Pagado',
            self::CANCELLED => 'Anulado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::IN_PREPARATION => 'blue',
            self::READY => 'green',
            self::DELIVERED => 'indigo',
            self::PAID => 'emerald',
            self::CANCELLED => 'red',
        };
    }

    public static function activeStatuses(): array
    {
        return [
            self::PENDING,
            self::IN_PREPARATION,
            self::READY,
            self::DELIVERED,
        ];
    }
}
