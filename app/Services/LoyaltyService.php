<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\LoyaltyPoint;
use App\Models\Order;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class LoyaltyService
{
    // Configuración del programa de lealtad
    protected array $config;

    public function __construct()
    {
        // Cargar configuración (podría venir de settings)
        $this->config = [
            // Puntos por cada $1000 gastados
            'points_per_amount' => config('loyalty.points_per_amount', 1),
            'amount_for_points' => config('loyalty.amount_for_points', 1000),
            
            // Valor de cada punto en pesos
            'point_value' => config('loyalty.point_value', 100),
            
            // Mínimo de puntos para redimir
            'min_points_redeem' => config('loyalty.min_points_redeem', 100),
            
            // Expiración en días (0 = no expira)
            'expiration_days' => config('loyalty.expiration_days', 365),
            
            // Niveles de lealtad
            'levels' => [
                'bronze' => ['min_points' => 0, 'multiplier' => 1.0, 'benefits' => []],
                'silver' => ['min_points' => 500, 'multiplier' => 1.2, 'benefits' => ['birthday_bonus']],
                'gold' => ['min_points' => 2000, 'multiplier' => 1.5, 'benefits' => ['birthday_bonus', 'priority_reservation']],
                'platinum' => ['min_points' => 5000, 'multiplier' => 2.0, 'benefits' => ['birthday_bonus', 'priority_reservation', 'exclusive_events']],
            ],
        ];
    }

    /**
     * Calcular puntos por una compra
     */
    public function calculatePointsForPurchase(float $amount, Customer $customer): int
    {
        if ($amount <= 0) return 0;

        // Puntos base
        $basePoints = floor($amount / $this->config['amount_for_points']) * $this->config['points_per_amount'];

        // Aplicar multiplicador por nivel
        $level = $this->getCustomerLevel($customer);
        $multiplier = $this->config['levels'][$level]['multiplier'] ?? 1.0;

        return (int) floor($basePoints * $multiplier);
    }

    /**
     * Acumular puntos por una orden
     */
    public function earnPoints(Customer $customer, Order $order): int
    {
        $points = $this->calculatePointsForPurchase((float) $order->total, $customer);
        
        if ($points <= 0) return 0;

        $expiresAt = $this->config['expiration_days'] > 0 
            ? now()->addDays($this->config['expiration_days']) 
            : null;

        LoyaltyPoint::create([
            'customer_id' => $customer->id,
            'order_id' => $order->id,
            'type' => LoyaltyPoint::TYPE_EARNED,
            'points' => $points,
            'balance_after' => $customer->loyalty_points + $points,
            'description' => 'Puntos por orden #' . $order->order_number,
            'expires_at' => $expiresAt,
        ]);

        $customer->increment('loyalty_points', $points);

        // Actualizar nivel si es necesario
        $this->updateCustomerLevel($customer->fresh());

        return $points;
    }

    /**
     * Canjear puntos
     */
    public function redeemPoints(Customer $customer, int $points, ?Order $order = null, string $description = 'Canje de puntos'): array
    {
        // Validaciones
        if ($points <= 0) {
            return ['success' => false, 'message' => 'Los puntos deben ser mayores a 0'];
        }

        if ($points < $this->config['min_points_redeem']) {
            return ['success' => false, 'message' => "Mínimo {$this->config['min_points_redeem']} puntos para canjear"];
        }

        $availablePoints = $this->getAvailablePoints($customer);
        if ($points > $availablePoints) {
            return ['success' => false, 'message' => "No tienes suficientes puntos. Disponibles: {$availablePoints}"];
        }

        // Calcular valor del descuento
        $discountValue = $this->calculateRedemptionValue($points);

        // Registrar canje
        LoyaltyPoint::create([
            'customer_id' => $customer->id,
            'order_id' => $order?->id,
            'type' => LoyaltyPoint::TYPE_REDEEMED,
            'points' => -$points,
            'balance_after' => $customer->loyalty_points - $points,
            'description' => $description,
        ]);

        $customer->decrement('loyalty_points', $points);

        return [
            'success' => true,
            'points_redeemed' => $points,
            'discount_value' => $discountValue,
            'message' => "Canjeaste {$points} puntos por un descuento de $" . number_format($discountValue, 0, ',', '.'),
        ];
    }

    /**
     * Calcular valor de canje
     */
    public function calculateRedemptionValue(int $points): float
    {
        return $points * $this->config['point_value'];
    }

    /**
     * Calcular puntos necesarios para un descuento
     */
    public function calculatePointsNeeded(float $discountAmount): int
    {
        return (int) ceil($discountAmount / $this->config['point_value']);
    }

    /**
     * Obtener puntos disponibles (no expirados)
     */
    public function getAvailablePoints(Customer $customer): int
    {
        // Si no hay expiración, usar el total
        if ($this->config['expiration_days'] == 0) {
            return $customer->loyalty_points;
        }

        // Calcular puntos válidos (no expirados)
        $earned = $customer->loyaltyPoints()
            ->earned()
            ->notExpired()
            ->sum('points');

        $redeemed = abs($customer->loyaltyPoints()
            ->redeemed()
            ->sum('points'));

        return max(0, $earned - $redeemed);
    }

    /**
     * Obtener nivel del cliente
     */
    public function getCustomerLevel(Customer $customer): string
    {
        $totalPoints = $customer->loyalty_points;
        $currentLevel = 'bronze';

        foreach ($this->config['levels'] as $level => $data) {
            if ($totalPoints >= $data['min_points']) {
                $currentLevel = $level;
            }
        }

        return $currentLevel;
    }

    /**
     * Actualizar nivel del cliente
     */
    public function updateCustomerLevel(Customer $customer): void
    {
        $newLevel = $this->getCustomerLevel($customer);
        
        if ($customer->loyalty_level !== $newLevel) {
            $customer->update(['loyalty_level' => $newLevel]);
        }
    }

    /**
     * Agregar puntos de bonus
     */
    public function addBonusPoints(Customer $customer, int $points, string $reason): int
    {
        if ($points <= 0) return 0;

        $expiresAt = $this->config['expiration_days'] > 0 
            ? now()->addDays($this->config['expiration_days']) 
            : null;

        LoyaltyPoint::create([
            'customer_id' => $customer->id,
            'type' => LoyaltyPoint::TYPE_BONUS,
            'points' => $points,
            'balance_after' => $customer->loyalty_points + $points,
            'description' => $reason,
            'expires_at' => $expiresAt,
        ]);

        $customer->increment('loyalty_points', $points);
        $this->updateCustomerLevel($customer->fresh());

        return $points;
    }

    /**
     * Bonus de cumpleaños
     */
    public function giveBirthdayBonus(Customer $customer, int $bonusPoints = 50): ?int
    {
        $level = $this->getCustomerLevel($customer);
        
        if (!in_array('birthday_bonus', $this->config['levels'][$level]['benefits'] ?? [])) {
            return null;
        }

        // Verificar si ya recibió el bonus este año
        $hasReceivedThisYear = $customer->loyaltyPoints()
            ->where('type', LoyaltyPoint::TYPE_BONUS)
            ->where('description', 'like', '%cumpleaños%')
            ->whereYear('created_at', now()->year)
            ->exists();

        if ($hasReceivedThisYear) {
            return null;
        }

        return $this->addBonusPoints($customer, $bonusPoints, 'Bonus de cumpleaños');
    }

    /**
     * Puntos por referido
     */
    public function addReferralPoints(Customer $referrer, Customer $newCustomer, int $points = 100): int
    {
        LoyaltyPoint::create([
            'customer_id' => $referrer->id,
            'type' => LoyaltyPoint::TYPE_REFERRAL,
            'points' => $points,
            'balance_after' => $referrer->loyalty_points + $points,
            'description' => "Referido: {$newCustomer->getFullName()}",
            'expires_at' => $this->config['expiration_days'] > 0 
                ? now()->addDays($this->config['expiration_days']) 
                : null,
        ]);

        $referrer->increment('loyalty_points', $points);
        
        return $points;
    }

    /**
     * Expirar puntos vencidos
     */
    public function expirePoints(): int
    {
        $expiredRecords = LoyaltyPoint::where('type', LoyaltyPoint::TYPE_EARNED)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        $totalExpired = 0;

        foreach ($expiredRecords->groupBy('customer_id') as $customerId => $records) {
            $customer = Customer::find($customerId);
            if (!$customer) continue;

            $pointsToExpire = $records->sum('points');
            
            // Verificar si esos puntos aún están disponibles
            $availablePoints = $customer->loyalty_points;
            $actualExpired = min($pointsToExpire, $availablePoints);

            if ($actualExpired > 0) {
                LoyaltyPoint::create([
                    'customer_id' => $customer->id,
                    'type' => LoyaltyPoint::TYPE_EXPIRED,
                    'points' => -$actualExpired,
                    'balance_after' => $customer->loyalty_points - $actualExpired,
                    'description' => 'Puntos expirados',
                ]);

                $customer->decrement('loyalty_points', $actualExpired);
                $totalExpired += $actualExpired;
            }

            // Marcar registros como procesados (actualizar expires_at a null o eliminar)
            LoyaltyPoint::whereIn('id', $records->pluck('id'))->update(['expires_at' => null]);
        }

        return $totalExpired;
    }

    /**
     * Obtener historial de puntos de un cliente
     */
    public function getPointsHistory(Customer $customer, int $limit = 20): Collection
    {
        return $customer->loyaltyPoints()
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener próximos puntos a expirar
     */
    public function getExpiringPoints(Customer $customer, int $days = 30): array
    {
        $expiringPoints = $customer->loyaltyPoints()
            ->earned()
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<=', now()->addDays($days))
            ->get();

        return [
            'total' => $expiringPoints->sum('points'),
            'earliest_expiry' => $expiringPoints->min('expires_at'),
            'records' => $expiringPoints,
        ];
    }

    /**
     * Información del nivel
     */
    public function getLevelInfo(string $level): array
    {
        return $this->config['levels'][$level] ?? $this->config['levels']['bronze'];
    }

    /**
     * Puntos para siguiente nivel
     */
    public function getPointsToNextLevel(Customer $customer): ?array
    {
        $currentLevel = $this->getCustomerLevel($customer);
        $levels = array_keys($this->config['levels']);
        $currentIndex = array_search($currentLevel, $levels);

        if ($currentIndex === count($levels) - 1) {
            return null; // Ya está en el nivel máximo
        }

        $nextLevel = $levels[$currentIndex + 1];
        $nextLevelData = $this->config['levels'][$nextLevel];
        $pointsNeeded = $nextLevelData['min_points'] - $customer->loyalty_points;

        return [
            'next_level' => $nextLevel,
            'points_needed' => max(0, $pointsNeeded),
            'current_points' => $customer->loyalty_points,
            'target_points' => $nextLevelData['min_points'],
        ];
    }

    /**
     * Resumen del programa para un cliente
     */
    public function getCustomerSummary(Customer $customer): array
    {
        $level = $this->getCustomerLevel($customer);
        $levelInfo = $this->getLevelInfo($level);
        $nextLevelInfo = $this->getPointsToNextLevel($customer);
        $expiringPoints = $this->getExpiringPoints($customer);

        return [
            'total_points' => $customer->loyalty_points,
            'available_points' => $this->getAvailablePoints($customer),
            'level' => $level,
            'level_label' => ucfirst($level),
            'level_benefits' => $levelInfo['benefits'],
            'multiplier' => $levelInfo['multiplier'],
            'next_level' => $nextLevelInfo,
            'expiring_soon' => $expiringPoints,
            'point_value' => $this->config['point_value'],
            'redemption_value' => $this->calculateRedemptionValue($this->getAvailablePoints($customer)),
        ];
    }

    /**
     * Obtener estadísticas del cliente
     */
    public function getCustomerStats(Customer $customer): array
    {
        $totalEarned = $customer->loyaltyPoints()->where('type', LoyaltyPoint::TYPE_EARNED)->sum('points');
        $totalRedeemed = abs($customer->loyaltyPoints()->where('type', LoyaltyPoint::TYPE_REDEEMED)->sum('points'));
        $totalExpired = abs($customer->loyaltyPoints()->where('type', LoyaltyPoint::TYPE_EXPIRED)->sum('points'));
        $totalBonus = $customer->loyaltyPoints()->whereIn('type', [LoyaltyPoint::TYPE_BONUS, LoyaltyPoint::TYPE_REFERRAL])->sum('points');

        $firstTransaction = $customer->loyaltyPoints()->oldest()->first();
        $lastTransaction = $customer->loyaltyPoints()->latest()->first();

        $monthlyAverage = $customer->loyaltyPoints()
            ->where('type', LoyaltyPoint::TYPE_EARNED)
            ->where('created_at', '>=', now()->subMonths(6))
            ->sum('points') / 6;

        return [
            'total_earned' => $totalEarned,
            'total_redeemed' => $totalRedeemed,
            'total_expired' => $totalExpired,
            'total_bonus' => $totalBonus,
            'current_balance' => $customer->loyalty_points,
            'first_activity' => $firstTransaction?->created_at,
            'last_activity' => $lastTransaction?->created_at,
            'monthly_average' => round($monthlyAverage),
            'total_transactions' => $customer->loyaltyPoints()->count(),
        ];
    }

    /**
     * Expirar puntos antiguos (alias para expirePoints con conteo de registros)
     */
    public function expireOldPoints(): int
    {
        $expiredRecords = LoyaltyPoint::where('type', LoyaltyPoint::TYPE_EARNED)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->count();

        if ($expiredRecords > 0) {
            $this->expirePoints();
        }

        return $expiredRecords;
    }
}
