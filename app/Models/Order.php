<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'table_id',
        'customer_id',
        'user_id',
        'cash_session_id',
        'order_number',
        'type',
        'status',
        'payment_status',
        'guests',
        'subtotal',
        'discount_amount',
        'discount_type',
        'discount_reason',
        'discount_authorized_by',
        'tax_amount',
        'tip_amount',
        'delivery_fee',
        'total',
        'paid_amount',
        'notes',
        'kitchen_notes',
        'source',
        'external_id',
        'scheduled_at',
        'completed_at',
        'preparation_started_at',
        'ready_at',
        'served_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => OrderType::class,
            'status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'tip_amount' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
            'preparation_started_at' => 'datetime',
            'ready_at' => 'datetime',
            'served_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber($order->branch_id);
            }
        });
    }

    // Relaciones
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Alias for backward compatibility
    public function waiter(): BelongsTo
    {
        return $this->user();
    }

    // Alias for cashier (same as waiter in this system)
    public function cashier(): BelongsTo
    {
        return $this->user();
    }

    public function cashSession(): BelongsTo
    {
        return $this->belongsTo(CashSession::class);
    }

    public function discountAuthorizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'discount_authorized_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(OrderDiscount::class);
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(OrderTax::class);
    }

    public function tips(): HasMany
    {
        return $this->hasMany(Tip::class);
    }

    public function invoice(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', OrderStatus::activeStatuses());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeForKitchen($query)
    {
        return $query->whereIn('status', [OrderStatus::PENDING, OrderStatus::IN_PREPARATION]);
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', PaymentStatus::PENDING);
    }

    // Helpers
    public static function generateOrderNumber(int $branchId): string
    {
        $prefix = str_pad($branchId, 2, '0', STR_PAD_LEFT);
        $date = now()->format('ymd');
        $sequence = static::where('branch_id', $branchId)
            ->whereDate('created_at', today())
            ->count() + 1;

        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type->label();
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->items->sum('total');
        $taxAmount = $this->items->sum('tax_amount');

        $this->subtotal = $subtotal;
        $this->tax_amount = $taxAmount;
        $this->total = $subtotal - $this->discount_amount + $this->tip_amount + $this->delivery_fee;
        $this->save();
    }

    public function getBalanceDue(): float
    {
        return max(0, $this->total - $this->paid_amount);
    }

    public function isPaid(): bool
    {
        return $this->paid_amount >= $this->total;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [OrderStatus::PENDING, OrderStatus::IN_PREPARATION]);
    }

    public function markAsPaid(): void
    {
        $this->payment_status = PaymentStatus::COMPLETED;
        $this->status = OrderStatus::PAID;
        $this->completed_at = now();
        $this->save();
    }

    public function getKitchenItems()
    {
        return $this->items()
            ->whereIn('status', ['pending', 'preparing'])
            ->with('product')
            ->get();
    }
}
