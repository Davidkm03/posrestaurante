<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'order_id',
        'customer_id',
        'resolution_id',
        'user_id',
        'invoice_number',
        'prefix',
        'invoice_type',
        'issue_date',
        'issue_time',
        'due_date',
        'payment_form',
        'payment_method_code',
        'currency_code',
        'exchange_rate',
        'subtotal',
        'total_discount',
        'total_tax_iva',
        'total_tax_inc',
        'total_tax_other',
        'total_withholdings',
        'total',
        'notes',
        'cufe',
        'qr_data',
        'status',
        'dian_response',
        'dian_track_id',
        'xml_path',
        'pdf_path',
        'email_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => InvoiceStatus::class,
            'issue_date' => 'date',
            'due_date' => 'date',
            'exchange_rate' => 'decimal:6',
            'subtotal' => 'decimal:2',
            'total_discount' => 'decimal:2',
            'total_tax_iva' => 'decimal:2',
            'total_tax_inc' => 'decimal:2',
            'total_tax_other' => 'decimal:2',
            'total_withholdings' => 'decimal:2',
            'total' => 'decimal:2',
            'email_sent_at' => 'datetime',
        ];
    }

    // Relaciones
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
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
        return $this->hasMany(InvoiceLine::class)->orderBy('line_number');
    }

    public function creditNotes(): HasMany
    {
        return $this->hasMany(CreditNote::class);
    }

    public function debitNotes(): HasMany
    {
        return $this->hasMany(DebitNote::class);
    }

    public function dianDocument(): MorphOne
    {
        return $this->morphOne(DIANDocument::class, 'documentable');
    }

    /**
     * Get payments through the order relationship
     */
    public function payments()
    {
        return $this->hasManyThrough(
            Payment::class,
            Order::class,
            'id', // Foreign key on orders table
            'order_id', // Foreign key on payments table
            'order_id', // Local key on invoices table
            'id' // Local key on orders table
        );
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', InvoiceStatus::APPROVED);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [InvoiceStatus::PENDING, InvoiceStatus::SENT]);
    }

    // Helpers
    public function getFullNumber(): string
    {
        return $this->prefix ? "{$this->prefix}{$this->invoice_number}" : $this->invoice_number;
    }

    public function canBeVoided(): bool
    {
        return $this->status->canBeVoided() && $this->creditNotes()->count() === 0;
    }

    public function getTotalTaxes(): float
    {
        return $this->total_tax_iva + $this->total_tax_inc + $this->total_tax_other;
    }

    public function getNetTotal(): float
    {
        return $this->subtotal - $this->total_discount;
    }

    public function isApprovedByDIAN(): bool
    {
        return $this->status === InvoiceStatus::APPROVED;
    }

    public function markAsApproved(string $trackId = null): void
    {
        $this->status = InvoiceStatus::APPROVED;
        $this->dian_track_id = $trackId;
        $this->save();
    }

    public function markAsRejected(string $response): void
    {
        $this->status = InvoiceStatus::REJECTED;
        $this->dian_response = $response;
        $this->save();
    }
}
