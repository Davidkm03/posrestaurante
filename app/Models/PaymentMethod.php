<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'dian_code',
        'requires_reference',
        'opens_cash_drawer',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'requires_reference' => 'boolean',
            'opens_cash_drawer' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function isCash(): bool
    {
        return $this->type === 'cash';
    }
}
