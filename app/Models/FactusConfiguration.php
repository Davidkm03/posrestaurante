<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class FactusConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'client_id',
        'client_secret',
        'email',
        'password',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'is_sandbox',
        'is_active',
        'auto_send_invoice',
        'auto_send_email',
        'default_numbering_range_id',
        'credit_note_range_id',
        'company_info',
        'subscription_info',
        'last_sync_at',
    ];

    protected function casts(): array
    {
        return [
            'is_sandbox' => 'boolean',
            'is_active' => 'boolean',
            'auto_send_invoice' => 'boolean',
            'auto_send_email' => 'boolean',
            'company_info' => 'array',
            'subscription_info' => 'array',
            'token_expires_at' => 'datetime',
            'last_sync_at' => 'datetime',
        ];
    }

    // Encriptar campos sensibles
    public function setClientSecretAttribute($value): void
    {
        $this->attributes['client_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getClientSecretAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getPasswordAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setAccessTokenAttribute($value): void
    {
        $this->attributes['access_token'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getAccessTokenAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setRefreshTokenAttribute($value): void
    {
        $this->attributes['refresh_token'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getRefreshTokenAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    // Relaciones
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // Helpers
    public function isTokenExpired(): bool
    {
        if (!$this->token_expires_at) {
            return true;
        }

        return $this->token_expires_at->isPast();
    }

    public function hasValidCredentials(): bool
    {
        return !empty($this->client_id) 
            && !empty($this->client_secret) 
            && !empty($this->email) 
            && !empty($this->password);
    }

    public function getStatusLabel(): string
    {
        if (!$this->is_active) {
            return 'Inactivo';
        }

        if (!$this->hasValidCredentials()) {
            return 'Sin configurar';
        }

        if ($this->isTokenExpired()) {
            return 'Token expirado';
        }

        return 'Conectado';
    }

    public function getStatusColor(): string
    {
        return match($this->getStatusLabel()) {
            'Conectado' => 'green',
            'Token expirado' => 'yellow',
            'Sin configurar' => 'gray',
            default => 'red',
        };
    }
}
