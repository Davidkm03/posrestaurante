<?php

namespace App\Enums;

enum DocumentType: string
{
    case CC = '13';
    case NIT = '31';
    case CE = '22';
    case PASSPORT = '41';
    case TI = '12';
    case FOREIGN_NIT = '50';

    public function label(): string
    {
        return match($this) {
            self::CC => 'Cédula de Ciudadanía',
            self::NIT => 'NIT',
            self::CE => 'Cédula de Extranjería',
            self::PASSPORT => 'Pasaporte',
            self::TI => 'Tarjeta de Identidad',
            self::FOREIGN_NIT => 'NIT de Extranjero',
        };
    }

    public function shortLabel(): string
    {
        return match($this) {
            self::CC => 'C.C.',
            self::NIT => 'NIT',
            self::CE => 'C.E.',
            self::PASSPORT => 'Pasaporte',
            self::TI => 'T.I.',
            self::FOREIGN_NIT => 'NIT Ext.',
        };
    }

    public function requiresVerificationDigit(): bool
    {
        return $this === self::NIT;
    }
}
