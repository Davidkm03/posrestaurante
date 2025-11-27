<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactusNumberingRange extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'factus_id',
        'document_type',
        'prefix',
        'from_number',
        'to_number',
        'current_number',
        'resolution_number',
        'resolution_date',
        'valid_from',
        'valid_to',
        'technical_key',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'from_number' => 'integer',
        'to_number' => 'integer',
        'current_number' => 'integer',
        'resolution_date' => 'date',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Branch relationship
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Verificar si el rango está vigente
     */
    public function isValid(): bool
    {
        $now = now()->toDateString();
        
        if ($this->valid_from && $this->valid_from > $now) {
            return false;
        }
        
        if ($this->valid_to && $this->valid_to < $now) {
            return false;
        }
        
        return true;
    }

    /**
     * Verificar si tiene números disponibles
     */
    public function hasAvailableNumbers(): bool
    {
        return $this->current_number < $this->to_number;
    }

    /**
     * Obtener porcentaje de uso
     */
    public function getUsagePercentage(): float
    {
        $total = $this->to_number - $this->from_number;
        if ($total <= 0) {
            return 100;
        }
        
        $used = $this->current_number - $this->from_number;
        return round(($used / $total) * 100, 2);
    }

    /**
     * Obtener números restantes
     */
    public function getRemainingNumbers(): int
    {
        return max(0, $this->to_number - $this->current_number);
    }

    /**
     * Scope para rangos activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para rangos de un tipo de documento
     */
    public function scopeForDocumentType($query, string $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope para rangos válidos (vigentes y con números disponibles)
     */
    public function scopeValid($query)
    {
        $now = now()->toDateString();
        
        return $query->active()
            ->where(function ($q) use ($now) {
                $q->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('valid_to')
                    ->orWhere('valid_to', '>=', $now);
            })
            ->whereRaw('current_number < to_number');
    }
}
