<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case SENT = 'sent';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case VOIDED = 'voided';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Borrador',
            self::PENDING => 'Pendiente',
            self::SENT => 'Enviada',
            self::APPROVED => 'Aprobada DIAN',
            self::REJECTED => 'Rechazada DIAN',
            self::VOIDED => 'Anulada',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::PENDING => 'yellow',
            self::SENT => 'blue',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::VOIDED => 'purple',
        };
    }

    public function canBeVoided(): bool
    {
        return in_array($this, [self::APPROVED]);
    }
}
