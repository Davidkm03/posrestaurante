<?php

namespace App\Enums;

enum TableStatus: string
{
    case FREE = 'free';
    case OCCUPIED = 'occupied';
    case RESERVED = 'reserved';
    case BILL_REQUESTED = 'bill_requested';
    case CLEANING = 'cleaning';

    public function label(): string
    {
        return match($this) {
            self::FREE => 'Libre',
            self::OCCUPIED => 'Ocupada',
            self::RESERVED => 'Reservada',
            self::BILL_REQUESTED => 'Cuenta Pedida',
            self::CLEANING => 'Por Limpiar',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::FREE => 'green',
            self::OCCUPIED => 'red',
            self::RESERVED => 'blue',
            self::BILL_REQUESTED => 'yellow',
            self::CLEANING => 'gray',
        };
    }

    public function isAvailable(): bool
    {
        return $this === self::FREE;
    }
}
