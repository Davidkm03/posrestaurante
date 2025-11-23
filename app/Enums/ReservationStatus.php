<?php

namespace App\Enums;

enum ReservationStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case SEATED = 'seated';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no_show';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendiente',
            self::CONFIRMED => 'Confirmada',
            self::SEATED => 'Sentados',
            self::COMPLETED => 'Completada',
            self::CANCELLED => 'Cancelada',
            self::NO_SHOW => 'No Asistió',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::CONFIRMED => 'blue',
            self::SEATED => 'green',
            self::COMPLETED => 'gray',
            self::CANCELLED => 'red',
            self::NO_SHOW => 'orange',
        };
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [self::PENDING, self::CONFIRMED]);
    }
}
