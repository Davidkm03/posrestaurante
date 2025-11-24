<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'table_id',
        'customer_id',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'reservation_date',
        'reservation_time',
        'party_size',
        'duration_minutes',
        'status',
        'special_requests',
        'internal_notes',
        'deposit_amount',
        'deposit_paid',
        'confirmed_at',
        'reminder_sent_at',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'reservation_time' => 'datetime:H:i',
        'party_size' => 'integer',
        'duration_minutes' => 'integer',
        'status' => ReservationStatus::class,
        'deposit_amount' => 'decimal:2',
        'deposit_paid' => 'boolean',
        'confirmed_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    protected $appends = ['datetime', 'end_time', 'status_label', 'status_color'];

    // Relationships
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getDatetimeAttribute(): Carbon
    {
        return Carbon::parse($this->reservation_date->format('Y-m-d') . ' ' . $this->reservation_time->format('H:i:s'));
    }

    public function getEndTimeAttribute(): Carbon
    {
        return $this->datetime->addMinutes($this->duration_minutes);
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    // Scopes
    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('reservation_date', $date);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('reservation_date', [$startDate, $endDate]);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('reservation_date', '>=', now()->toDateString())
                     ->whereIn('status', [
                         ReservationStatus::PENDING->value,
                         ReservationStatus::CONFIRMED->value,
                     ])
                     ->orderBy('reservation_date')
                     ->orderBy('reservation_time');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('reservation_date', now()->toDateString());
    }

    public function scopePending($query)
    {
        return $query->where('status', ReservationStatus::PENDING->value);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', ReservationStatus::CONFIRMED->value);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            ReservationStatus::PENDING->value,
            ReservationStatus::CONFIRMED->value,
            ReservationStatus::SEATED->value,
        ]);
    }

    // Methods
    public function confirm(): bool
    {
        if (!$this->status->canBeCancelled()) {
            return false;
        }

        $this->update([
            'status' => ReservationStatus::CONFIRMED,
            'confirmed_at' => now(),
        ]);

        return true;
    }

    public function cancel(string $reason = null): bool
    {
        if (!$this->status->canBeCancelled()) {
            return false;
        }

        $this->update([
            'status' => ReservationStatus::CANCELLED,
            'internal_notes' => $reason
                ? ($this->internal_notes . "\nCancelación: " . $reason)
                : $this->internal_notes,
        ]);

        // Free the table if assigned
        if ($this->table) {
            $this->table->update(['status' => 'free']);
        }

        return true;
    }

    public function seat(): bool
    {
        if ($this->status !== ReservationStatus::CONFIRMED) {
            return false;
        }

        $this->update(['status' => ReservationStatus::SEATED]);

        // Mark table as occupied
        if ($this->table) {
            $this->table->update(['status' => 'occupied']);
        }

        return true;
    }

    public function complete(): bool
    {
        if ($this->status !== ReservationStatus::SEATED) {
            return false;
        }

        $this->update(['status' => ReservationStatus::COMPLETED]);

        return true;
    }

    public function markNoShow(): bool
    {
        if (!in_array($this->status, [ReservationStatus::PENDING, ReservationStatus::CONFIRMED])) {
            return false;
        }

        $this->update(['status' => ReservationStatus::NO_SHOW]);

        // Free the table if assigned
        if ($this->table) {
            $this->table->update(['status' => 'free']);
        }

        return true;
    }

    public function assignTable(int $tableId): bool
    {
        $table = Table::find($tableId);

        if (!$table || $table->capacity < $this->party_size) {
            return false;
        }

        $this->update(['table_id' => $tableId]);

        if ($this->status === ReservationStatus::CONFIRMED &&
            $this->datetime->isToday() &&
            $this->datetime->diffInMinutes(now()) <= 30) {
            $table->update(['status' => 'reserved']);
        }

        return true;
    }

    public function isOverlapping(int $tableId, $date, $time, int $duration, ?int $excludeId = null): bool
    {
        $startTime = Carbon::parse($date . ' ' . $time);
        $endTime = $startTime->copy()->addMinutes($duration);

        $query = static::where('table_id', $tableId)
            ->whereDate('reservation_date', $date)
            ->whereIn('status', [
                ReservationStatus::PENDING->value,
                ReservationStatus::CONFIRMED->value,
                ReservationStatus::SEATED->value,
            ]);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get()->contains(function ($reservation) use ($startTime, $endTime) {
            $resStart = $reservation->datetime;
            $resEnd = $reservation->end_time;

            return $startTime < $resEnd && $endTime > $resStart;
        });
    }

    public function needsReminder(): bool
    {
        if ($this->reminder_sent_at) {
            return false;
        }

        if (!in_array($this->status, [ReservationStatus::PENDING, ReservationStatus::CONFIRMED])) {
            return false;
        }

        // Send reminder 24 hours before
        return $this->datetime->diffInHours(now()) <= 24 && $this->datetime->isFuture();
    }
}
