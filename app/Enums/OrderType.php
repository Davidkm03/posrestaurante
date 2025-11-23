<?php

namespace App\Enums;

enum OrderType: string
{
    case DINE_IN = 'dine_in';
    case TAKEAWAY = 'takeaway';
    case DELIVERY = 'delivery';
    case PLATFORM = 'platform';

    public function label(): string
    {
        return match($this) {
            self::DINE_IN => 'Mesa',
            self::TAKEAWAY => 'Para Llevar',
            self::DELIVERY => 'Domicilio',
            self::PLATFORM => 'Plataforma',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::DINE_IN => 'table',
            self::TAKEAWAY => 'shopping-bag',
            self::DELIVERY => 'truck',
            self::PLATFORM => 'globe',
        };
    }
}
