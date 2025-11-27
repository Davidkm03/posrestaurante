<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactusMunicipality extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'department_code',
        'department_name',
    ];

    protected $casts = [
        'code' => 'integer',
    ];

    /**
     * Obtener nombre completo (municipio, departamento)
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->name}, {$this->department_name}";
    }

    /**
     * Scope para buscar por nombre
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%")
            ->orWhere('department_name', 'like', "%{$term}%");
    }

    /**
     * Scope para filtrar por departamento
     */
    public function scopeForDepartment($query, string $departmentCode)
    {
        return $query->where('department_code', $departmentCode);
    }

    /**
     * Obtener municipios agrupados por departamento
     */
    public static function groupedByDepartment(): array
    {
        return static::orderBy('department_name')
            ->orderBy('name')
            ->get()
            ->groupBy('department_name')
            ->toArray();
    }
}
