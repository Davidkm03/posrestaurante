<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CashRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(CashSession::class);
    }

    public function currentSession(): HasOne
    {
        return $this->hasOne(CashSession::class)->where('status', 'open');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function hasOpenSession(): bool
    {
        return $this->currentSession()->exists();
    }

    public function getOpenSession(): ?CashSession
    {
        return $this->currentSession;
    }
}
