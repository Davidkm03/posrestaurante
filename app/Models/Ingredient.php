<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingredient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name',
        'sku',
        'description',
        'unit',
        'unit_cost',
        'stock',
        'min_stock',
        'max_stock',
        'category',
        'is_active',
        'expiry_date',
    ];

    protected function casts(): array
    {
        return [
            'unit_cost' => 'decimal:4',
            'stock' => 'decimal:3',
            'min_stock' => 'decimal:3',
            'max_stock' => 'decimal:3',
            'is_active' => 'boolean',
            'expiry_date' => 'date',
        ];
    }

    // Relaciones
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function recipeItems(): HasMany
    {
        return $this->hasMany(RecipeItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock <= min_stock');
    }

    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays($days));
    }

    // Accessors
    public function getStockValueAttribute(): float
    {
        return $this->stock * $this->unit_cost;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) return 'out';
        if ($this->stock <= $this->min_stock) return 'low';
        return 'ok';
    }

    public function getStockStatusColorAttribute(): string
    {
        return match($this->stock_status) {
            'out' => 'red',
            'low' => 'yellow',
            'ok' => 'green',
        };
    }

    // Métodos
    public function adjustStock(float $quantity, string $type, ?int $userId = null, ?string $reason = null): void
    {
        $stockBefore = $this->stock;
        $this->stock += $quantity;
        $this->save();

        // Registrar movimiento (si tienes la tabla para ingredientes)
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}
