<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'name',
        'unit_price',
        'quantity',
        'discount_amount',
        'tax_amount',
        'total',
        'status',
        'kitchen_status',
        'is_ready',
        'notes',
        'is_courtesy',
        'courtesy_reason',
        'sent_by',
        'sent_at',
        'prepared_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'quantity' => 'decimal:3',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'is_courtesy' => 'boolean',
            'is_ready' => 'boolean',
            'sent_at' => 'datetime',
            'prepared_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    /**
     * Accessor para obtener el nombre del producto
     */
    public function getProductNameAttribute(): string
    {
        return $this->name ?? $this->product?->name ?? 'Producto';
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($item) {
            // Only recalculate order totals, not item totals
            // Item totals should be calculated when modifiers are added
            if ($item->order) {
                $item->order->recalculateTotals();
            }
        });

        static::deleted(function ($item) {
            if ($item->order) {
                $item->order->recalculateTotals();
            }
        });
    }

    // Relaciones
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function modifiers(): HasMany
    {
        return $this->hasMany(OrderItemModifier::class);
    }

    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    // Helpers
    public function calculateTotal(): void
    {
        $modifiersTotal = $this->modifiers->sum(fn($m) => $m->price * $m->quantity);
        $subtotal = ($this->unit_price + $modifiersTotal) * $this->quantity;

        if ($this->is_courtesy) {
            $this->total = 0;
            $this->tax_amount = 0;
        } else {
            $this->total = $subtotal - $this->discount_amount;
            // El tax_amount ya debería venir calculado según el producto
        }
    }

    public function getModifiersDescription(): string
    {
        if ($this->modifiers->isEmpty()) {
            return '';
        }

        return $this->modifiers->pluck('name')->implode(', ');
    }

    public function markAsPreparing(): void
    {
        $this->status = 'preparing';
        $this->save();
    }

    public function markAsReady(): void
    {
        $this->status = 'ready';
        $this->prepared_at = now();
        $this->save();
    }

    public function markAsDelivered(): void
    {
        $this->status = 'delivered';
        $this->delivered_at = now();
        $this->save();
    }
}
