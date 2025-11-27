<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactusProductTribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'rate',
    ];

    protected $casts = [
        'code' => 'integer',
        'rate' => 'decimal:2',
    ];

    /**
     * Obtener tributos para IVA
     */
    public static function ivaOptions(): array
    {
        return static::whereIn('code', [1, 2, 3, 4, 5, 6, 7, 8]) // Códigos estándar de IVA en Colombia
            ->orderBy('rate', 'desc')
            ->pluck('name', 'code')
            ->toArray();
    }

    /**
     * Scope para tributos activos/principales
     */
    public function scopeMain($query)
    {
        // IVA 19%, 5%, 0%
        return $query->whereIn('code', [1, 2, 3]);
    }
}
