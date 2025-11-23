<?php

namespace App\Enums;

enum CustomerType: string
{
    case NATURAL = 'natural';
    case JURIDICA = 'juridica';

    public function label(): string
    {
        return match($this) {
            self::NATURAL => 'Persona Natural',
            self::JURIDICA => 'Persona Jurídica',
        };
    }

    public function dianCode(): string
    {
        return match($this) {
            self::NATURAL => '1',
            self::JURIDICA => '2',
        };
    }
}
