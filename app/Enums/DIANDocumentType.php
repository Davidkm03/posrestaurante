<?php

namespace App\Enums;

enum DIANDocumentType: string
{
    case INVOICE = '01';
    case CREDIT_NOTE = '91';
    case DEBIT_NOTE = '92';
    case SUPPORT_DOCUMENT = '05';

    public function label(): string
    {
        return match($this) {
            self::INVOICE => 'Factura Electrónica',
            self::CREDIT_NOTE => 'Nota Crédito',
            self::DEBIT_NOTE => 'Nota Débito',
            self::SUPPORT_DOCUMENT => 'Documento Soporte',
        };
    }

    public function prefix(): string
    {
        return match($this) {
            self::INVOICE => 'FE',
            self::CREDIT_NOTE => 'NC',
            self::DEBIT_NOTE => 'ND',
            self::SUPPORT_DOCUMENT => 'DS',
        };
    }

    public function ublVersion(): string
    {
        return 'UBL 2.1';
    }
}
