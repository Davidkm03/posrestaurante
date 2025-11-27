<?php

namespace App\Models;

use App\Enums\CustomerType;
use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'customer_type',
        'document_type',
        'document_number',
        'verification_digit',
        'first_name',
        'last_name',
        'business_name',
        'email',
        'phone',
        'mobile',
        'address',
        'city',
        'department',
        'postal_code',
        'tax_regime',
        'fiscal_responsibilities',
        'economic_activity',
        'notes',
        'allergies',
        'preferences',
        'loyalty_points',
        'loyalty_level',
        'credit_limit',
        'credit_balance',
        'birthdate',
        'accepts_marketing',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'customer_type' => CustomerType::class,
            'document_type' => DocumentType::class,
            'fiscal_responsibilities' => 'array',
            'loyalty_points' => 'integer',
            'credit_limit' => 'decimal:2',
            'credit_balance' => 'decimal:2',
            'birthdate' => 'date',
            'accepts_marketing' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // Relaciones
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function loyaltyPoints(): HasMany
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors
    public function getNameAttribute(): string
    {
        return $this->getFullName();
    }

    public function getFullNameAttribute(): string
    {
        return $this->getFullName();
    }

    // Helpers
    public function getFullName(): string
    {
        if ($this->customer_type === CustomerType::JURIDICA) {
            return $this->business_name ?? $this->first_name;
        }
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getDocumentWithDV(): string
    {
        if ($this->document_type === DocumentType::NIT && $this->verification_digit) {
            return "{$this->document_number}-{$this->verification_digit}";
        }
        return $this->document_number;
    }

    public function getDefaultAddress(): ?CustomerAddress
    {
        return $this->addresses()->where('is_default', true)->first()
            ?? $this->addresses()->first();
    }

    public function addLoyaltyPoints(int $points, ?Order $order = null, string $description = null): void
    {
        $this->loyaltyPoints()->create([
            'order_id' => $order?->id,
            'type' => 'earned',
            'points' => $points,
            'balance_after' => $this->loyalty_points + $points,
            'description' => $description ?? 'Puntos por compra',
            'expires_at' => now()->addYear(),
        ]);

        $this->increment('loyalty_points', $points);
    }

    public function redeemLoyaltyPoints(int $points, ?Order $order = null): bool
    {
        if ($this->loyalty_points < $points) {
            return false;
        }

        $this->loyaltyPoints()->create([
            'order_id' => $order?->id,
            'type' => 'redeemed',
            'points' => -$points,
            'balance_after' => $this->loyalty_points - $points,
            'description' => 'Redención de puntos',
        ]);

        $this->decrement('loyalty_points', $points);
        return true;
    }

    public function getTotalPurchases(): float
    {
        return $this->orders()->where('payment_status', 'completed')->sum('total');
    }

    public function getOrdersCount(): int
    {
        return $this->orders()->count();
    }

    public function getAverageTicket(): float
    {
        $count = $this->getOrdersCount();
        if ($count === 0) return 0;
        return $this->getTotalPurchases() / $count;
    }
}
