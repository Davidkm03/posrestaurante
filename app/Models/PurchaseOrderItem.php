<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'ingredient_id',
        'quantity_ordered',
        'quantity_received',
        'unit_cost',
        'total',
        'unit',
    ];

    protected function casts(): array
    {
        return [
            'quantity_ordered' => 'decimal:3',
            'quantity_received' => 'decimal:3',
            'unit_cost' => 'decimal:4',
            'total' => 'decimal:2',
        ];
    }

    // Relaciones
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    // Accessors
    public function getPendingQuantityAttribute(): float
    {
        return $this->quantity_ordered - $this->quantity_received;
    }

    public function getIsCompleteAttribute(): bool
    {
        return $this->quantity_received >= $this->quantity_ordered;
    }

    // Métodos
    public function receiveQuantity(float $quantity): void
    {
        $this->quantity_received += $quantity;
        $this->save();
    }

    public function calculateTotal(): void
    {
        $this->total = $this->quantity_ordered * $this->unit_cost;
        $this->save();
    }
}
