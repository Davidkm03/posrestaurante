<?php

namespace App\Enums;

enum TaxType: string
{
    case IVA = '01';
    case INC = '04';
    case ICA = '03';
    case EXEMPT = '00';

    public function label(): string
    {
        return match($this) {
            self::IVA => 'IVA',
            self::INC => 'Impoconsumo',
            self::ICA => 'ICA',
            self::EXEMPT => 'Exento',
        };
    }

    public function defaultPercentage(): float
    {
        return match($this) {
            self::IVA => 19.00,
            self::INC => 8.00,
            self::ICA => 0.00,
            self::EXEMPT => 0.00,
        };
    }

    public function dianName(): string
    {
        return match($this) {
            self::IVA => 'IVA',
            self::INC => 'INC',
            self::ICA => 'ICA',
            self::EXEMPT => 'ZZ',
        };
    }
}
