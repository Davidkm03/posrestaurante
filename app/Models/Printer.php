<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Printer extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'type', // receipt, kitchen, bar
        'connection_type', // network, usb, bluetooth
        'ip_address',
        'port',
        'device_path',
        'paper_width',
        'auto_cut',
        'open_drawer',
        'categories', // JSON para filtrar productos por categoría
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'port' => 'integer',
            'paper_width' => 'integer',
            'auto_cut' => 'boolean',
            'open_drawer' => 'boolean',
            'categories' => 'array',
            'is_active' => 'boolean',
        ];
    }

    // Relaciones
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeForType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeReceipt($query)
    {
        return $query->where('type', 'receipt');
    }

    public function scopeKitchen($query)
    {
        return $query->where('type', 'kitchen');
    }

    // Helpers
    public function getConnectionString(): string
    {
        return match ($this->connection_type) {
            'network' => "{$this->ip_address}:{$this->port}",
            'usb' => $this->device_path ?? 'USB Connection',
            'bluetooth' => 'Bluetooth Connection',
            default => 'Unknown',
        };
    }

    public function getConnectionTypeLabel(): string
    {
        return match ($this->connection_type) {
            'network' => 'Red (Ethernet/WiFi)',
            'usb' => 'USB',
            'bluetooth' => 'Bluetooth',
            default => 'Desconocido',
        };
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'receipt' => 'Tickets de Venta',
            'kitchen' => 'Comandas Cocina',
            'bar' => 'Comandas Bar',
            default => 'General',
        };
    }

    public function getPaperWidthChars(): int
    {
        return $this->paper_width === 58 ? 32 : 48;
    }

    /**
     * Obtener impresora para un tipo
     */
    public static function getForType(string $type, ?int $branchId = null): ?self
    {
        $branchId = $branchId ?? session('current_branch_id');

        return static::active()
            ->forBranch($branchId)
            ->forType($type)
            ->first();
    }
}
