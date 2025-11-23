<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DIANResolution extends Model
{
    use HasFactory;

    protected $table = 'dian_resolutions';

    protected $fillable = [
        'branch_id',
        'resolution_number',
        'resolution_date',
        'prefix',
        'range_from',
        'range_to',
        'current_number',
        'technical_key',
        'valid_from',
        'valid_to',
        'environment',
        'document_type',
        'is_active',
        'is_contingency',
    ];

    protected function casts(): array
    {
        return [
            'resolution_date' => 'date',
            'valid_from' => 'date',
            'valid_to' => 'date',
            'is_active' => 'boolean',
            'is_contingency' => 'boolean',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'resolution_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->where('valid_to', '>=', now()->toDateString());
    }

    public function getNextNumber(): int
    {
        return $this->current_number + 1;
    }

    public function incrementNumber(): int
    {
        $this->increment('current_number');
        return $this->current_number;
    }

    public function getRemainingNumbers(): int
    {
        return $this->range_to - $this->current_number;
    }

    public function getUsagePercentage(): float
    {
        $total = $this->range_to - $this->range_from + 1;
        $used = $this->current_number - $this->range_from + 1;
        return round(($used / $total) * 100, 2);
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->valid_to->diffInDays(now()) <= $days;
    }

    public function isRunningLow(int $threshold = 100): bool
    {
        return $this->getRemainingNumbers() <= $threshold;
    }

    public function isValid(): bool
    {
        return $this->is_active
            && $this->valid_to >= now()
            && $this->current_number < $this->range_to;
    }
}
