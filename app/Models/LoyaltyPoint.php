<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoyaltyPoint extends Model
{
    protected $fillable = [
        'customer_id',
        'order_id',
        'type',          // earned, redeemed, expired, adjusted
        'points',        // Positivo para ganar, negativo para redimir
        'balance_after',
        'description',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'points' => 'integer',
        'balance_after' => 'integer',
    ];

    // Tipos de transacción
    const TYPE_EARNED = 'earned';
    const TYPE_REDEEMED = 'redeemed';
    const TYPE_EXPIRED = 'expired';
    const TYPE_ADJUSTED = 'adjusted';
    const TYPE_BONUS = 'bonus';
    const TYPE_REFERRAL = 'referral';

    // Relaciones
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Scopes
    public function scopeEarned($query)
    {
        return $query->where('type', self::TYPE_EARNED);
    }

    public function scopeRedeemed($query)
    {
        return $query->where('type', self::TYPE_REDEEMED);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    // Helpers
    public function isExpired(): bool
    {
        if (!$this->expires_at) return false;
        return $this->expires_at->isPast();
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            self::TYPE_EARNED => 'Ganados',
            self::TYPE_REDEEMED => 'Canjeados',
            self::TYPE_EXPIRED => 'Expirados',
            self::TYPE_ADJUSTED => 'Ajuste',
            self::TYPE_BONUS => 'Bonus',
            self::TYPE_REFERRAL => 'Referido',
            default => 'Otro',
        };
    }

    public function getTypeColor(): string
    {
        return match($this->type) {
            self::TYPE_EARNED => 'green',
            self::TYPE_REDEEMED => 'blue',
            self::TYPE_EXPIRED => 'red',
            self::TYPE_ADJUSTED => 'yellow',
            self::TYPE_BONUS => 'purple',
            self::TYPE_REFERRAL => 'indigo',
            default => 'gray',
        };
    }
}
