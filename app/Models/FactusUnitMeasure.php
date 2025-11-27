<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactusUnitMeasure extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
    ];

    protected $casts = [
        'code' => 'integer',
    ];

    /**
     * Obtener etiqueta completa
     */
    public function getLabelAttribute(): string
    {
        if ($this->symbol) {
            return "{$this->name} ({$this->symbol})";
        }
        return $this->name;
    }

    /**
     * Scope para unidades comunes en restaurantes
     */
    public function scopeCommon($query)
    {
        // Unidades típicas: unidad, porción, plato, servicio, etc.
        return $query->whereIn('code', [70, 94, 886, 111, 100]); 
    }

    /**
     * Obtener opciones para select
     */
    public static function selectOptions(): array
    {
        return static::orderBy('name')
            ->get()
            ->mapWithKeys(function ($unit) {
                return [$unit->code => $unit->label];
            })
            ->toArray();
    }
}
