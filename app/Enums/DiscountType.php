<?php

namespace App\Enums;

enum DiscountType: string
{
    case PERCENTAGE = 'percentage';
    case FIXED = 'fixed';
    case COURTESY = 'courtesy';

    public function label(): string
    {
        return match($this) {
            self::PERCENTAGE => 'Porcentaje',
            self::FIXED => 'Valor Fijo',
            self::COURTESY => 'Cortesía',
        };
    }

    public function calculate(float $amount, float $value): float
    {
        return match($this) {
            self::PERCENTAGE => $amount * ($value / 100),
            self::FIXED => min($value, $amount),
            self::COURTESY => $amount,
        };
    }
}
