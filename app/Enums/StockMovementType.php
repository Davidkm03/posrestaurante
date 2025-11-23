<?php

namespace App\Enums;

enum StockMovementType: string
{
    case PURCHASE = 'purchase';
    case SALE = 'sale';
    case RETURN_SUPPLIER = 'return_supplier';
    case RETURN_CUSTOMER = 'return_customer';
    case ADJUSTMENT_IN = 'adjustment_in';
    case ADJUSTMENT_OUT = 'adjustment_out';
    case TRANSFER_IN = 'transfer_in';
    case TRANSFER_OUT = 'transfer_out';
    case WASTE = 'waste';
    case INTERNAL_USE = 'internal_use';

    public function label(): string
    {
        return match($this) {
            self::PURCHASE => 'Compra',
            self::SALE => 'Venta',
            self::RETURN_SUPPLIER => 'Devolución a Proveedor',
            self::RETURN_CUSTOMER => 'Devolución de Cliente',
            self::ADJUSTMENT_IN => 'Ajuste Entrada',
            self::ADJUSTMENT_OUT => 'Ajuste Salida',
            self::TRANSFER_IN => 'Transferencia Entrada',
            self::TRANSFER_OUT => 'Transferencia Salida',
            self::WASTE => 'Merma/Desperdicio',
            self::INTERNAL_USE => 'Consumo Interno',
        };
    }

    public function isIncoming(): bool
    {
        return in_array($this, [
            self::PURCHASE,
            self::RETURN_CUSTOMER,
            self::ADJUSTMENT_IN,
            self::TRANSFER_IN,
        ]);
    }

    public function isOutgoing(): bool
    {
        return !$this->isIncoming();
    }

    public function affectsStock(): int
    {
        return $this->isIncoming() ? 1 : -1;
    }
}
