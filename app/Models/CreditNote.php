<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'invoice_id',
        'customer_id',
        'resolution_id',
        'user_id',
        'credit_note_number',
        'prefix',
        'issue_date',
        'issue_time',
        'reason_code',
        'reason_description',
        'subtotal',
        'total_discount',
        'total_tax_iva',
        'total_tax_inc',
        'total',
        'cufe',
        'dian_cufe',
        'dian_uuid',
        'dian_status',
        'dian_response',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'subtotal' => 'decimal:2',
            'total_discount' => 'decimal:2',
            'total_tax_iva' => 'decimal:2',
            'total_tax_inc' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function resolution(): BelongsTo
    {
        return $this->belongsTo(DIANResolution::class, 'resolution_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(CreditNoteLine::class);
    }

    public function getFullNumber(): string
    {
        return $this->prefix ? "{$this->prefix}{$this->credit_note_number}" : $this->credit_note_number;
    }
}
