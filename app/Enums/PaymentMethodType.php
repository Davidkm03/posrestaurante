<?php

namespace App\Enums;

enum PaymentMethodType: string
{
    case CASH = 'cash';
    case CREDIT_CARD = 'credit_card';
    case DEBIT_CARD = 'debit_card';
    case TRANSFER = 'transfer';
    case NEQUI = 'nequi';
    case DAVIPLATA = 'daviplata';
    case VOUCHER = 'voucher';
    case LOYALTY_POINTS = 'loyalty_points';
    case CREDIT = 'credit';

    public function label(): string
    {
        return match($this) {
            self::CASH => 'Efectivo',
            self::CREDIT_CARD => 'Tarjeta Crédito',
            self::DEBIT_CARD => 'Tarjeta Débito',
            self::TRANSFER => 'Transferencia',
            self::NEQUI => 'Nequi',
            self::DAVIPLATA => 'Daviplata',
            self::VOUCHER => 'Bono/Vale',
            self::LOYALTY_POINTS => 'Puntos',
            self::CREDIT => 'Crédito',
        };
    }

    public function dianCode(): string
    {
        return match($this) {
            self::CASH => '10',
            self::CREDIT_CARD => '48',
            self::DEBIT_CARD => '49',
            self::TRANSFER, self::NEQUI, self::DAVIPLATA => 'ZZZ',
            self::VOUCHER => 'ZZZ',
            self::LOYALTY_POINTS => 'ZZZ',
            self::CREDIT => '30',
        };
    }

    public function requiresReference(): bool
    {
        return match($this) {
            self::CASH, self::LOYALTY_POINTS => false,
            default => true,
        };
    }
}
