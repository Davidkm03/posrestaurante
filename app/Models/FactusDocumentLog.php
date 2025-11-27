<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactusDocumentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'invoice_id',
        'document_type',
        'document_number',
        'uuid',
        'cufe',
        'status',
        'request_payload',
        'response_data',
        'error_message',
        'pdf_url',
        'xml_url',
        'email_sent',
        'validated_at',
    ];

    protected function casts(): array
    {
        return [
            'request_payload' => 'array',
            'response_data' => 'array',
            'email_sent' => 'boolean',
            'validated_at' => 'datetime',
        ];
    }

    // Relaciones
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    // Scopes
    public function scopeValidated($query)
    {
        return $query->where('status', 'validated');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeErrors($query)
    {
        return $query->whereIn('status', ['rejected', 'error']);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('document_type', $type);
    }

    // Helpers
    public function isValidated(): bool
    {
        return $this->status === 'validated';
    }

    public function hasError(): bool
    {
        return in_array($this->status, ['rejected', 'error']);
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Pendiente',
            'validated' => 'Validado',
            'rejected' => 'Rechazado',
            'error' => 'Error',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'validated' => 'green',
            'pending' => 'yellow',
            'rejected', 'error' => 'red',
            default => 'gray',
        };
    }

    public function getDocumentTypeLabel(): string
    {
        return match($this->document_type) {
            'invoice' => 'Factura Electrónica',
            'credit_note' => 'Nota Crédito',
            'debit_note' => 'Nota Débito',
            'support_document' => 'Documento Soporte',
            default => ucfirst($this->document_type),
        };
    }
}
