<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'cash_session_id',
        'user_id',
        'payment_method_id',
        'status',
        'amount',
        'received_amount',
        'change_amount',
        'reference',
        'authorization_code',
        'gateway_response',
        'notes',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'amount' => 'decimal:2',
            'received_amount' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'gateway_response' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($payment) {
            if ($payment->status === PaymentStatus::COMPLETED) {
                $payment->order->increment('paid_amount', $payment->amount);

                if ($payment->order->isPaid()) {
                    $payment->order->markAsPaid();
                }
            }
        });
    }

    // Relaciones
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function cashSession(): BelongsTo
    {
        return $this->belongsTo(CashSession::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function tip(): HasOne
    {
        return $this->hasOne(Tip::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function cashMovement(): HasOne
    {
        return $this->hasOne(CashMovement::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', PaymentStatus::COMPLETED);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('paid_at', today());
    }

    // Helpers
    public function canBeRefunded(): bool
    {
        if ($this->status !== PaymentStatus::COMPLETED) {
            return false;
        }

        $totalRefunded = $this->refunds()->sum('amount');
        return $totalRefunded < $this->amount;
    }

    public function getRefundableAmount(): float
    {
        $totalRefunded = $this->refunds()->sum('amount');
        return max(0, $this->amount - $totalRefunded);
    }

    public function isCash(): bool
    {
        return $this->paymentMethod?->type === 'cash';
    }
}
