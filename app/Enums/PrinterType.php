<?php

namespace App\Enums;

enum PrinterType: string
{
    case RECEIPT = 'receipt';
    case KITCHEN = 'kitchen';
    case BAR = 'bar';
    case LABEL = 'label';

    public function label(): string
    {
        return match($this) {
            self::RECEIPT => 'Recibos/Facturas',
            self::KITCHEN => 'Cocina',
            self::BAR => 'Bar',
            self::LABEL => 'Etiquetas',
        };
    }

    public function defaultWidth(): int
    {
        return match($this) {
            self::RECEIPT => 80,
            self::KITCHEN => 80,
            self::BAR => 80,
            self::LABEL => 58,
        };
    }
}
