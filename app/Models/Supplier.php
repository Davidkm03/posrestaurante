<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'document_type',
        'document_number',
        'verification_digit',
        'contact_name',
        'email',
        'phone',
        'mobile',
        'address',
        'city',
        'tax_regime',
        'notes',
        'credit_limit',
        'payment_days',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'credit_limit' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // Relaciones
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors
    public function getFullDocumentAttribute(): string
    {
        $doc = $this->document_number;
        if ($this->verification_digit) {
            $doc .= '-' . $this->verification_digit;
        }
        return $doc ?: 'Sin documento';
    }

    public function getFullContactAttribute(): string
    {
        $parts = array_filter([
            $this->phone,
            $this->mobile,
            $this->email,
        ]);
        return implode(' | ', $parts) ?: 'Sin contacto';
    }

    // Métodos
    public function getTotalPurchases(): float
    {
        return $this->purchaseOrders()
            ->where('status', 'received')
            ->sum('total');
    }

    public function getPendingBalance(): float
    {
        return $this->purchaseOrders()
            ->where('status', 'received')
            ->whereNull('paid_at')
            ->sum('total');
    }
}
