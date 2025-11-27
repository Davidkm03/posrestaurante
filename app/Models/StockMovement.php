<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'branch_id',
        'product_id',
        'user_id',
        'type',
        'quantity',
        'unit_cost',
        'total_cost',
        'stock_before',
        'stock_after',
        'reference_type',
        'reference_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => StockMovementType::class,
            'quantity' => 'decimal:2',
            'unit_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'stock_before' => 'decimal:2',
            'stock_after' => 'decimal:2',
        ];
    }

    // Relaciones
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Morphable reference
    public function reference()
    {
        if (!$this->reference_type || !$this->reference_id) {
            return null;
        }

        return $this->reference_type::find($this->reference_id);
    }

    // Scopes
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeIncoming($query)
    {
        return $query->whereIn('type', [
            StockMovementType::PURCHASE->value,
            StockMovementType::RETURN_CUSTOMER->value,
            StockMovementType::ADJUSTMENT_IN->value,
            StockMovementType::TRANSFER_IN->value,
        ]);
    }

    public function scopeOutgoing($query)
    {
        return $query->whereIn('type', [
            StockMovementType::SALE->value,
            StockMovementType::RETURN_SUPPLIER->value,
            StockMovementType::ADJUSTMENT_OUT->value,
            StockMovementType::TRANSFER_OUT->value,
            StockMovementType::WASTE->value,
            StockMovementType::INTERNAL_USE->value,
        ]);
    }

    public function scopeByType($query, StockMovementType $type)
    {
        return $query->where('type', $type->value);
    }

    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);
    }

    // Accessors
    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            StockMovementType::PURCHASE, 
            StockMovementType::RETURN_CUSTOMER, 
            StockMovementType::ADJUSTMENT_IN, 
            StockMovementType::TRANSFER_IN => 'green',
            
            StockMovementType::SALE => 'blue',
            
            StockMovementType::WASTE, 
            StockMovementType::ADJUSTMENT_OUT => 'red',
            
            default => 'gray',
        };
    }
}
