<?php

namespace App\Models;

use App\Enums\TaxType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'barcode',
        'description',
        'short_description',
        'price',
        'cost',
        'tax_type',
        'tax_percentage',
        'tax_included',
        'image',
        'gallery',
        'track_inventory',
        'stock',
        'min_stock',
        'unit',
        'is_combo',
        'is_active',
        'show_in_pos',
        'show_in_menu',
        'is_featured',
        'sort_order',
        'availability_schedule',
        'prep_time_minutes',
        'printer_destination',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost' => 'decimal:2',
            'tax_percentage' => 'decimal:2',
            'tax_included' => 'boolean',
            'gallery' => 'array',
            'track_inventory' => 'boolean',
            'is_combo' => 'boolean',
            'is_active' => 'boolean',
            'show_in_pos' => 'boolean',
            'show_in_menu' => 'boolean',
            'is_featured' => 'boolean',
            'availability_schedule' => 'array',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // Relaciones
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function modifierGroups(): BelongsToMany
    {
        return $this->belongsToMany(ModifierGroup::class, 'product_modifier_group')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order')
            ->withTimestamps();
    }

    public function recipe(): HasOne
    {
        return $this->hasOne(Recipe::class);
    }

    /**
     * Items incluidos cuando este producto es un combo
     */
    public function comboItems(): HasMany
    {
        return $this->hasMany(ComboItem::class, 'combo_id')->orderBy('sort_order');
    }

    /**
     * Productos incluidos en este combo (a través de comboItems)
     */
    public function comboProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'combo_items', 'combo_id', 'product_id')
            ->withPivot('quantity', 'sort_order')
            ->orderByPivot('sort_order')
            ->withTimestamps();
    }

    /**
     * Combos que incluyen este producto
     */
    public function includedInCombos(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'combo_items', 'product_id', 'combo_id')
            ->withPivot('quantity', 'sort_order')
            ->withTimestamps();
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForPos($query)
    {
        return $query->where('show_in_pos', true)->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->where('track_inventory', false)
                ->orWhere('stock', '>', 0);
        });
    }

    // Helpers
    public function getPriceWithoutTax(): float
    {
        if ($this->tax_included) {
            return round($this->price / (1 + ($this->tax_percentage / 100)), 2);
        }
        return $this->price;
    }

    public function getTaxAmount(): float
    {
        $basePrice = $this->getPriceWithoutTax();
        return round($basePrice * ($this->tax_percentage / 100), 2);
    }

    public function getPriceWithTax(): float
    {
        if ($this->tax_included) {
            return $this->price;
        }
        return $this->price + $this->getTaxAmount();
    }

    public function isAvailableNow(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->track_inventory && $this->stock <= 0) {
            return false;
        }

        if ($this->availability_schedule) {
            // Verificar disponibilidad por horario
            $now = now();
            $daySchedule = $this->availability_schedule[$now->dayOfWeek] ?? null;

            if ($daySchedule === null) {
                return true;
            }

            if ($daySchedule === false) {
                return false;
            }

            // Verificar horario específico
        }

        return true;
    }

    public function getProfit(): float
    {
        return $this->getPriceWithoutTax() - $this->cost;
    }

    public function getProfitMargin(): float
    {
        if ($this->cost <= 0) {
            return 100;
        }
        return round(($this->getProfit() / $this->cost) * 100, 2);
    }
}
