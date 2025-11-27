<?php

namespace App\Models;

use App\Enums\TableStatus;
use App\Events\TableStatusUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'zone_id',
        'branch_id',
        'number',
        'name',
        'capacity',
        'status',
        'position_x',
        'position_y',
        'shape',
        'width',
        'height',
        'is_active',
        'occupied_at',
        'current_order_id',
        'assigned_waiter_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => TableStatus::class,
            'is_active' => 'boolean',
            'occupied_at' => 'datetime',
        ];
    }

    // Relaciones
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function currentOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'current_order_id');
    }

    public function assignedWaiter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_waiter_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFree($query)
    {
        return $query->where('status', TableStatus::FREE);
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', TableStatus::OCCUPIED);
    }

    // Helpers
    public function getDisplayName(): string
    {
        return $this->name ?? "Mesa {$this->number}";
    }

    public function occupy(Order $order, ?User $waiter = null): void
    {
        $this->status = TableStatus::OCCUPIED;
        $this->current_order_id = $order->id;
        $this->occupied_at = now();
        $this->assigned_waiter_id = $waiter?->id ?? $order->user_id;
        $this->save();
        
        // Broadcast cambio de estado
        event(new TableStatusUpdated($this));
    }

    public function release(): void
    {
        $this->status = TableStatus::CLEANING;
        $this->current_order_id = null;
        $this->occupied_at = null;
        $this->save();
        
        // Broadcast cambio de estado
        event(new TableStatusUpdated($this));
    }

    public function markAsClean(): void
    {
        $this->status = TableStatus::FREE;
        $this->assigned_waiter_id = null;
        $this->save();
        
        // Broadcast cambio de estado
        event(new TableStatusUpdated($this));
    }

    public function getOccupiedDuration(): ?int
    {
        if (!$this->occupied_at) {
            return null;
        }
        return $this->occupied_at->diffInMinutes(now());
    }
}
