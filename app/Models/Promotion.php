<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Promotion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name',
        'description',
        'type',
        'discount_type',
        'discount_value',
        'min_purchase',
        'max_discount',
        'buy_quantity',
        'get_quantity',
        'applicable_products', // JSON: null = all, array = specific product ids
        'applicable_categories', // JSON: null = all, array = specific category ids
        'applicable_days', // JSON: [0,1,2,3,4,5,6] - 0 = Sunday
        'start_time',
        'end_time',
        'starts_at',
        'ends_at',
        'usage_limit',
        'usage_count',
        'usage_per_customer',
        'coupon_code',
        'is_active',
        'priority',
        'is_combinable',
    ];

    protected $casts = [
        'applicable_products' => 'array',
        'applicable_categories' => 'array',
        'applicable_days' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'is_active' => 'boolean',
        'is_combinable' => 'boolean',
    ];

    // Tipos de promoción
    const TYPE_PERCENTAGE = 'percentage';          // Porcentaje de descuento
    const TYPE_FIXED = 'fixed';                    // Monto fijo
    const TYPE_BUY_X_GET_Y = 'buy_x_get_y';       // Compra X lleva Y
    const TYPE_BUNDLE = 'bundle';                  // Combo especial
    const TYPE_HAPPY_HOUR = 'happy_hour';         // Happy hour
    const TYPE_FREE_SHIPPING = 'free_delivery';   // Delivery gratis

    // Relaciones
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function products()
    {
        if (!$this->applicable_products) {
            return Product::query();
        }
        return Product::whereIn('id', $this->applicable_products);
    }

    public function categories()
    {
        if (!$this->applicable_categories) {
            return Category::query();
        }
        return Category::whereIn('id', $this->applicable_categories);
    }

    public function usages()
    {
        return $this->hasMany(PromotionUsage::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query, ?Carbon $at = null)
    {
        $now = $at ?? now();
        
        return $query
            ->where('is_active', true)
            ->where(function($q) use ($now) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('usage_limit')
                  ->orWhereRaw('usage_count < usage_limit');
            });
    }

    public function scopeAvailableNow($query)
    {
        $now = now();
        $dayOfWeek = $now->dayOfWeek;
        $currentTime = $now->format('H:i:s');
        
        return $query->valid()
            ->where(function($q) use ($dayOfWeek) {
                $q->whereNull('applicable_days')
                  ->orWhereJsonContains('applicable_days', $dayOfWeek);
            })
            ->where(function($q) use ($currentTime) {
                $q->whereNull('start_time')
                  ->orWhere('start_time', '<=', $currentTime);
            })
            ->where(function($q) use ($currentTime) {
                $q->whereNull('end_time')
                  ->orWhere('end_time', '>=', $currentTime);
            });
    }

    // Helpers
    public function isValidNow(): bool
    {
        if (!$this->is_active) return false;

        $now = now();
        
        // Verificar fechas
        if ($this->starts_at && $this->starts_at > $now) return false;
        if ($this->ends_at && $this->ends_at < $now) return false;

        // Verificar límite de uso
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) return false;

        // Verificar día de la semana
        if ($this->applicable_days && !in_array($now->dayOfWeek, $this->applicable_days)) return false;

        // Verificar hora
        $currentTime = $now->format('H:i:s');
        if ($this->start_time && $currentTime < $this->start_time) return false;
        if ($this->end_time && $currentTime > $this->end_time) return false;

        return true;
    }

    public function isApplicableToProduct(Product $product): bool
    {
        // Si no hay restricciones de productos o categorías, aplica a todos
        if (!$this->applicable_products && !$this->applicable_categories) {
            return true;
        }

        // Verificar productos específicos
        if ($this->applicable_products && in_array($product->id, $this->applicable_products)) {
            return true;
        }

        // Verificar categorías
        if ($this->applicable_categories && in_array($product->category_id, $this->applicable_categories)) {
            return true;
        }

        return false;
    }

    public function canBeUsedByCustomer(?Customer $customer): bool
    {
        if (!$this->usage_per_customer || !$customer) {
            return true;
        }

        $usagesByCustomer = $this->usages()
            ->where('customer_id', $customer->id)
            ->count();

        return $usagesByCustomer < $this->usage_per_customer;
    }

    public function calculateDiscount(float $subtotal, array $items = []): float
    {
        if (!$this->isValidNow()) {
            return 0;
        }

        // Verificar compra mínima
        if ($this->min_purchase && $subtotal < $this->min_purchase) {
            return 0;
        }

        $discount = match($this->type) {
            self::TYPE_PERCENTAGE => $this->calculatePercentageDiscount($subtotal),
            self::TYPE_FIXED => $this->discount_value,
            self::TYPE_BUY_X_GET_Y => $this->calculateBuyXGetYDiscount($items),
            self::TYPE_HAPPY_HOUR => $this->calculatePercentageDiscount($subtotal),
            default => 0,
        };

        // Aplicar límite máximo de descuento
        if ($this->max_discount && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }

        return $discount;
    }

    protected function calculatePercentageDiscount(float $subtotal): float
    {
        return $subtotal * ($this->discount_value / 100);
    }

    protected function calculateBuyXGetYDiscount(array $items): float
    {
        // Implementación de Buy X Get Y
        $totalDiscount = 0;
        
        foreach ($items as $item) {
            if (!$this->isApplicableToProduct($item['product'] ?? null)) {
                continue;
            }

            $quantity = $item['quantity'] ?? 0;
            $unitPrice = $item['unit_price'] ?? 0;
            
            // Calcular cuántos "sets" de la promoción aplican
            $buyQty = $this->buy_quantity ?? 1;
            $getQty = $this->get_quantity ?? 1;
            $setSize = $buyQty + $getQty;
            
            $sets = floor($quantity / $setSize);
            $freeItems = $sets * $getQty;
            
            $totalDiscount += $freeItems * $unitPrice;
        }

        return $totalDiscount;
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            self::TYPE_PERCENTAGE => 'Porcentaje',
            self::TYPE_FIXED => 'Monto Fijo',
            self::TYPE_BUY_X_GET_Y => 'Compra X Lleva Y',
            self::TYPE_BUNDLE => 'Combo',
            self::TYPE_HAPPY_HOUR => 'Happy Hour',
            self::TYPE_FREE_SHIPPING => 'Delivery Gratis',
            default => 'Otro',
        };
    }

    public function getStatusBadge(): array
    {
        if (!$this->is_active) {
            return ['color' => 'gray', 'text' => 'Inactiva'];
        }

        if ($this->starts_at && $this->starts_at > now()) {
            return ['color' => 'yellow', 'text' => 'Programada'];
        }

        if ($this->ends_at && $this->ends_at < now()) {
            return ['color' => 'red', 'text' => 'Expirada'];
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return ['color' => 'orange', 'text' => 'Agotada'];
        }

        return ['color' => 'green', 'text' => 'Activa'];
    }
}
