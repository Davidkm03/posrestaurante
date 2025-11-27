<?php

namespace App\Services;

use App\Models\Promotion;
use App\Models\PromotionUsage;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class PromotionService
{
    /**
     * Obtener promociones activas para una sucursal
     */
    public function getActivePromotions(int $branchId): Collection
    {
        return Promotion::where('branch_id', $branchId)
            ->availableNow()
            ->orderBy('priority', 'desc')
            ->get();
    }

    /**
     * Obtener promociones aplicables a un carrito
     */
    public function getApplicablePromotions(int $branchId, array $items, ?Customer $customer = null): Collection
    {
        $promotions = $this->getActivePromotions($branchId);
        
        return $promotions->filter(function($promotion) use ($items, $customer) {
            // Verificar si el cliente puede usar la promoción
            if (!$promotion->canBeUsedByCustomer($customer)) {
                return false;
            }

            // Verificar si al menos un item es aplicable
            foreach ($items as $item) {
                $product = $item['product'] ?? Product::find($item['product_id'] ?? null);
                if ($product && $promotion->isApplicableToProduct($product)) {
                    return true;
                }
            }

            // Si no hay restricción de productos, aplica a todo
            if (!$promotion->applicable_products && !$promotion->applicable_categories) {
                return true;
            }

            return false;
        });
    }

    /**
     * Calcular descuento total para un pedido
     */
    public function calculateDiscount(
        int $branchId,
        float $subtotal,
        array $items = [],
        ?Customer $customer = null,
        ?string $couponCode = null
    ): array {
        $result = [
            'total_discount' => 0,
            'applied_promotions' => [],
            'errors' => [],
        ];

        // Si hay código de cupón, buscar esa promoción específica
        if ($couponCode) {
            $couponPromo = Promotion::where('branch_id', $branchId)
                ->where('coupon_code', $couponCode)
                ->first();

            if (!$couponPromo) {
                $result['errors'][] = 'Código de cupón no válido';
                return $result;
            }

            if (!$couponPromo->isValidNow()) {
                $result['errors'][] = 'El cupón ha expirado o no está activo';
                return $result;
            }

            if (!$couponPromo->canBeUsedByCustomer($customer)) {
                $result['errors'][] = 'Ya has usado este cupón el máximo número de veces';
                return $result;
            }

            $discount = $couponPromo->calculateDiscount($subtotal, $items);
            if ($discount > 0) {
                $result['total_discount'] = $discount;
                $result['applied_promotions'][] = [
                    'id' => $couponPromo->id,
                    'name' => $couponPromo->name,
                    'discount' => $discount,
                    'type' => $couponPromo->type,
                ];
            }

            return $result;
        }

        // Obtener promociones automáticas (sin código)
        $promotions = Promotion::where('branch_id', $branchId)
            ->whereNull('coupon_code')
            ->availableNow()
            ->orderBy('priority', 'desc')
            ->get();

        $appliedNonCombinable = false;

        foreach ($promotions as $promotion) {
            // Si ya se aplicó una no combinable, saltar
            if ($appliedNonCombinable && !$promotion->is_combinable) {
                continue;
            }

            // Verificar si el cliente puede usarla
            if (!$promotion->canBeUsedByCustomer($customer)) {
                continue;
            }

            // Calcular descuento
            $discount = $promotion->calculateDiscount($subtotal, $items);

            if ($discount > 0) {
                $result['total_discount'] += $discount;
                $result['applied_promotions'][] = [
                    'id' => $promotion->id,
                    'name' => $promotion->name,
                    'discount' => $discount,
                    'type' => $promotion->type,
                ];

                if (!$promotion->is_combinable) {
                    $appliedNonCombinable = true;
                    break; // No aplicar más promociones
                }
            }
        }

        return $result;
    }

    /**
     * Aplicar promociones a una orden
     */
    public function applyToOrder(Order $order, array $appliedPromotions = []): void
    {
        foreach ($appliedPromotions as $promoData) {
            $promotion = Promotion::find($promoData['id']);
            
            if (!$promotion) continue;

            // Registrar uso
            PromotionUsage::create([
                'promotion_id' => $promotion->id,
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'discount_amount' => $promoData['discount'],
                'used_at' => now(),
            ]);

            // Incrementar contador de uso
            $promotion->incrementUsage();
        }
    }

    /**
     * Validar código de cupón
     */
    public function validateCoupon(string $code, int $branchId, ?Customer $customer = null): array
    {
        $promotion = Promotion::where('branch_id', $branchId)
            ->where('coupon_code', $code)
            ->first();

        if (!$promotion) {
            return [
                'valid' => false,
                'message' => 'Código de cupón no encontrado',
            ];
        }

        if (!$promotion->isValidNow()) {
            return [
                'valid' => false,
                'message' => 'Este cupón no está activo o ha expirado',
            ];
        }

        if (!$promotion->canBeUsedByCustomer($customer)) {
            return [
                'valid' => false,
                'message' => 'Ya has alcanzado el límite de uso de este cupón',
            ];
        }

        return [
            'valid' => true,
            'promotion' => $promotion,
            'message' => 'Cupón válido: ' . $promotion->name,
        ];
    }

    /**
     * Obtener promociones tipo Happy Hour activas ahora
     */
    public function getHappyHourPromotions(int $branchId): Collection
    {
        return Promotion::where('branch_id', $branchId)
            ->where('type', Promotion::TYPE_HAPPY_HOUR)
            ->availableNow()
            ->get();
    }

    /**
     * Verificar si hay Happy Hour activo
     */
    public function isHappyHourActive(int $branchId): bool
    {
        return $this->getHappyHourPromotions($branchId)->isNotEmpty();
    }

    /**
     * Crear una promoción
     */
    public function create(array $data): Promotion
    {
        return Promotion::create($data);
    }

    /**
     * Actualizar una promoción
     */
    public function update(Promotion $promotion, array $data): Promotion
    {
        $promotion->update($data);
        return $promotion->fresh();
    }

    /**
     * Duplicar una promoción
     */
    public function duplicate(Promotion $promotion): Promotion
    {
        $data = $promotion->toArray();
        unset($data['id'], $data['created_at'], $data['updated_at'], $data['usage_count']);
        
        $data['name'] = $data['name'] . ' (copia)';
        $data['is_active'] = false;
        $data['coupon_code'] = $data['coupon_code'] ? $data['coupon_code'] . '_COPY' : null;
        $data['usage_count'] = 0;

        return Promotion::create($data);
    }

    /**
     * Obtener estadísticas de promociones
     */
    public function getStats(int $branchId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now();

        $usages = PromotionUsage::whereHas('promotion', fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('used_at', [$startDate, $endDate])
            ->get();

        return [
            'total_uses' => $usages->count(),
            'total_discount' => $usages->sum('discount_amount'),
            'unique_customers' => $usages->pluck('customer_id')->filter()->unique()->count(),
            'top_promotions' => $usages->groupBy('promotion_id')
                ->map(fn($group) => [
                    'uses' => $group->count(),
                    'total_discount' => $group->sum('discount_amount'),
                ])
                ->sortByDesc('uses')
                ->take(5),
        ];
    }
}
