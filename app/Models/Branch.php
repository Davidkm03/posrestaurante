<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'nit',
        'address',
        'city',
        'department',
        'country',
        'phone',
        'email',
        'logo',
        'settings',
        'is_main',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'is_main' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // Relaciones
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_default')
            ->withTimestamps();
    }

    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class);
    }

    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cashRegisters(): HasMany
    {
        return $this->hasMany(CashRegister::class);
    }

    public function dianResolutions(): HasMany
    {
        return $this->hasMany(DIANResolution::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function printers(): HasMany
    {
        return $this->hasMany(Printer::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helpers
    public function getSetting(string $key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }

    public function getActiveResolution(string $documentType = '01'): ?DIANResolution
    {
        return $this->dianResolutions()
            ->where('document_type', $documentType)
            ->where('is_active', true)
            ->where('valid_to', '>=', now())
            ->where('current_number', '<', \DB::raw('range_to'))
            ->first();
    }
}
