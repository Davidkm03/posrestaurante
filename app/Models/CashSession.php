<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_register_id',
        'user_id',
        'closed_by',
        'opening_amount',
        'closing_amount',
        'expected_amount',
        'difference',
        'opening_breakdown',
        'closing_breakdown',
        'opening_notes',
        'closing_notes',
        'opened_at',
        'closed_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'opening_amount' => 'decimal:2',
            'closing_amount' => 'decimal:2',
            'expected_amount' => 'decimal:2',
            'difference' => 'decimal:2',
            'opening_breakdown' => 'array',
            'closing_breakdown' => 'array',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    // Relaciones
    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(CashMovement::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    // Helpers
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function calculateExpectedAmount(): float
    {
        $cashPayments = $this->payments()
            ->whereHas('paymentMethod', fn($q) => $q->where('type', 'cash'))
            ->where('status', 'completed')
            ->sum('amount');

        $cashIncomes = $this->movements()
            ->where('type', 'income')
            ->sum('amount');

        $cashExpenses = $this->movements()
            ->whereIn('type', ['expense', 'withdrawal'])
            ->sum('amount');

        return $this->opening_amount + $cashPayments + $cashIncomes - $cashExpenses;
    }

    public function getTotalSales(): float
    {
        return $this->payments()->where('status', 'completed')->sum('amount');
    }

    public function getSalesByPaymentMethod(): array
    {
        return $this->payments()
            ->where('status', 'completed')
            ->with('paymentMethod')
            ->get()
            ->groupBy('paymentMethod.name')
            ->map(fn($payments) => $payments->sum('amount'))
            ->toArray();
    }

    public function getTotalTips(): float
    {
        return $this->orders()->sum('tip_amount');
    }

    public function close(float $closingAmount, array $breakdown = null, string $notes = null): void
    {
        $this->expected_amount = $this->calculateExpectedAmount();
        $this->closing_amount = $closingAmount;
        $this->difference = $closingAmount - $this->expected_amount;
        $this->closing_breakdown = $breakdown;
        $this->closing_notes = $notes;
        $this->closed_at = now();
        $this->closed_by = auth()->id();
        $this->status = 'closed';
        $this->save();
    }
}
