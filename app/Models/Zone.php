<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'color',
        'description',
        'is_active',
        'sort_order',
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

    public function tables(): HasMany
    {
        return $this->hasMany(Table::class)->orderBy('number');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getAvailableTablesCount(): int
    {
        return $this->tables()->where('status', 'free')->count();
    }
}
