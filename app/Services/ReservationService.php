<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Table;
use App\Models\Customer;
use App\Enums\DocumentType;
use App\Enums\ReservationStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class ReservationService
{
    /**
     * Create a new reservation
     */
    public function create(array $data): Reservation
    {
        $branchId = $data['branch_id'] ?? session('current_branch_id');

        // Check for conflicts if table is specified
        if (!empty($data['table_id'])) {
            $this->validateTableAvailability(
                $data['table_id'],
                $data['reservation_date'],
                $data['reservation_time'],
                $data['duration_minutes'] ?? 90,
                $data['party_size']
            );
        }

        // Link or create customer
        $customerId = $this->resolveCustomer($data);

        $reservation = Reservation::create([
            'branch_id' => $branchId,
            'table_id' => $data['table_id'] ?? null,
            'customer_id' => $customerId,
            'user_id' => auth()->id(),
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'customer_email' => $data['customer_email'] ?? null,
            'reservation_date' => $data['reservation_date'],
            'reservation_time' => $data['reservation_time'],
            'party_size' => $data['party_size'],
            'duration_minutes' => $data['duration_minutes'] ?? 90,
            'status' => ReservationStatus::PENDING,
            'special_requests' => $data['special_requests'] ?? null,
            'internal_notes' => $data['internal_notes'] ?? null,
            'deposit_amount' => $data['deposit_amount'] ?? 0,
            'deposit_paid' => $data['deposit_paid'] ?? false,
        ]);

        // Auto-assign table if not specified
        if (!$reservation->table_id) {
            $this->autoAssignTable($reservation);
        }

        return $reservation;
    }

    /**
     * Update a reservation
     */
    public function update(Reservation $reservation, array $data): Reservation
    {
        // Check for conflicts if table or time changed
        if (isset($data['table_id']) || isset($data['reservation_date']) || isset($data['reservation_time'])) {
            $tableId = $data['table_id'] ?? $reservation->table_id;
            $date = $data['reservation_date'] ?? $reservation->reservation_date->format('Y-m-d');
            $time = $data['reservation_time'] ?? $reservation->reservation_time->format('H:i');
            $duration = $data['duration_minutes'] ?? $reservation->duration_minutes;
            $partySize = $data['party_size'] ?? $reservation->party_size;

            if ($tableId) {
                $this->validateTableAvailability($tableId, $date, $time, $duration, $partySize, $reservation->id);
            }
        }

        $reservation->update($data);

        return $reservation->fresh();
    }

    /**
     * Confirm a reservation
     */
    public function confirm(Reservation $reservation): Reservation
    {
        if (!$reservation->confirm()) {
            throw new \Exception('No se puede confirmar esta reservación');
        }

        // TODO: Send confirmation notification
        // $this->sendConfirmationNotification($reservation);

        return $reservation->fresh();
    }

    /**
     * Cancel a reservation
     */
    public function cancel(Reservation $reservation, ?string $reason = null): Reservation
    {
        if (!$reservation->cancel($reason)) {
            throw new \Exception('No se puede cancelar esta reservación');
        }

        // TODO: Send cancellation notification
        // $this->sendCancellationNotification($reservation);

        return $reservation->fresh();
    }

    /**
     * Mark customer as seated
     */
    public function seat(Reservation $reservation): Reservation
    {
        if (!$reservation->seat()) {
            throw new \Exception('No se puede marcar como sentados');
        }

        return $reservation->fresh();
    }

    /**
     * Complete a reservation
     */
    public function complete(Reservation $reservation): Reservation
    {
        if (!$reservation->complete()) {
            throw new \Exception('No se puede completar esta reservación');
        }

        return $reservation->fresh();
    }

    /**
     * Mark as no-show
     */
    public function markNoShow(Reservation $reservation): Reservation
    {
        if (!$reservation->markNoShow()) {
            throw new \Exception('No se puede marcar como no asistió');
        }

        return $reservation->fresh();
    }

    /**
     * Assign a table to reservation
     */
    public function assignTable(Reservation $reservation, int $tableId): Reservation
    {
        $this->validateTableAvailability(
            $tableId,
            $reservation->reservation_date->format('Y-m-d'),
            $reservation->reservation_time->format('H:i'),
            $reservation->duration_minutes,
            $reservation->party_size,
            $reservation->id
        );

        if (!$reservation->assignTable($tableId)) {
            throw new \Exception('No se puede asignar la mesa');
        }

        return $reservation->fresh();
    }

    /**
     * Get available time slots for a date
     */
    public function getAvailableSlots(int $branchId, string $date, int $partySize, int $duration = 90): array
    {
        $slots = [];
        $openTime = Carbon::parse($date . ' 11:00');
        $closeTime = Carbon::parse($date . ' 22:00');

        // Get all tables that can accommodate the party
        $tables = Table::where('branch_id', $branchId)
            ->where('is_active', true)
            ->where('capacity', '>=', $partySize)
            ->get();

        if ($tables->isEmpty()) {
            return [];
        }

        // Check every 30-minute slot
        $currentSlot = $openTime->copy();
        while ($currentSlot->copy()->addMinutes($duration) <= $closeTime) {
            $slotTime = $currentSlot->format('H:i');

            // Check if at least one table is available
            foreach ($tables as $table) {
                $reservation = new Reservation();
                if (!$reservation->isOverlapping($table->id, $date, $slotTime, $duration)) {
                    $slots[] = [
                        'time' => $slotTime,
                        'formatted' => $currentSlot->format('g:i A'),
                        'available_tables' => $this->countAvailableTables($branchId, $date, $slotTime, $duration, $partySize),
                    ];
                    break;
                }
            }

            $currentSlot->addMinutes(30);
        }

        return $slots;
    }

    /**
     * Get available tables for a specific time
     */
    public function getAvailableTables(int $branchId, string $date, string $time, int $partySize, int $duration = 90): Collection
    {
        $tables = Table::where('branch_id', $branchId)
            ->where('is_active', true)
            ->where('capacity', '>=', $partySize)
            ->with('zone')
            ->get();

        $reservation = new Reservation();

        return $tables->filter(function ($table) use ($reservation, $date, $time, $duration) {
            return !$reservation->isOverlapping($table->id, $date, $time, $duration);
        });
    }

    /**
     * Get today's reservations
     */
    public function getTodayReservations(int $branchId): Collection
    {
        return Reservation::forBranch($branchId)
            ->today()
            ->with(['table', 'customer'])
            ->orderBy('reservation_time')
            ->get();
    }

    /**
     * Get upcoming reservations
     */
    public function getUpcoming(int $branchId, int $days = 7): Collection
    {
        return Reservation::forBranch($branchId)
            ->forDateRange(now()->toDateString(), now()->addDays($days)->toDateString())
            ->active()
            ->with(['table', 'customer'])
            ->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->get();
    }

    /**
     * Get reservation statistics
     */
    public function getStats(int $branchId, ?string $startDate = null, ?string $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth()->toDateString();
        $endDate = $endDate ?? now()->endOfMonth()->toDateString();

        $reservations = Reservation::forBranch($branchId)
            ->forDateRange($startDate, $endDate)
            ->get();

        return [
            'total' => $reservations->count(),
            'completed' => $reservations->where('status', ReservationStatus::COMPLETED)->count(),
            'cancelled' => $reservations->where('status', ReservationStatus::CANCELLED)->count(),
            'no_show' => $reservations->where('status', ReservationStatus::NO_SHOW)->count(),
            'pending' => $reservations->where('status', ReservationStatus::PENDING)->count(),
            'confirmed' => $reservations->where('status', ReservationStatus::CONFIRMED)->count(),
            'average_party_size' => round($reservations->avg('party_size'), 1),
            'total_guests' => $reservations->whereIn('status', [
                ReservationStatus::COMPLETED,
                ReservationStatus::SEATED,
            ])->sum('party_size'),
            'completion_rate' => $reservations->count() > 0
                ? round(($reservations->where('status', ReservationStatus::COMPLETED)->count() / $reservations->count()) * 100, 1)
                : 0,
            'no_show_rate' => $reservations->count() > 0
                ? round(($reservations->where('status', ReservationStatus::NO_SHOW)->count() / $reservations->count()) * 100, 1)
                : 0,
        ];
    }

    /**
     * Auto-assign best available table
     */
    protected function autoAssignTable(Reservation $reservation): bool
    {
        $availableTables = $this->getAvailableTables(
            $reservation->branch_id,
            $reservation->reservation_date->format('Y-m-d'),
            $reservation->reservation_time->format('H:i'),
            $reservation->party_size,
            $reservation->duration_minutes
        );

        if ($availableTables->isEmpty()) {
            return false;
        }

        // Get smallest table that fits
        $bestTable = $availableTables->sortBy('capacity')->first();

        $reservation->update(['table_id' => $bestTable->id]);

        return true;
    }

    /**
     * Validate table availability
     */
    protected function validateTableAvailability(
        int $tableId,
        string $date,
        string $time,
        int $duration,
        int $partySize,
        ?int $excludeId = null
    ): void {
        $table = Table::findOrFail($tableId);

        if ($table->capacity < $partySize) {
            throw new \Exception("La mesa no tiene capacidad suficiente ({$table->capacity} personas)");
        }

        $reservation = new Reservation();
        if ($reservation->isOverlapping($tableId, $date, $time, $duration, $excludeId)) {
            throw new \Exception('La mesa ya tiene una reservación en ese horario');
        }
    }

    /**
     * Count available tables for a slot
     */
    protected function countAvailableTables(int $branchId, string $date, string $time, int $duration, int $partySize): int
    {
        return $this->getAvailableTables($branchId, $date, $time, $partySize, $duration)->count();
    }

    /**
     * Resolve or create customer
     */
    protected function resolveCustomer(array $data): ?int
    {
        if (!empty($data['customer_id'])) {
            return $data['customer_id'];
        }

        // Try to find existing customer by phone
        $customer = Customer::where('phone', $data['customer_phone'])->first();

        if ($customer) {
            return $customer->id;
        }

        // Create new customer if email provided
        if (!empty($data['customer_email'])) {
            // Separar nombre completo en first_name y last_name
            $nameParts = explode(' ', $data['customer_name'], 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? '';

            $customer = Customer::create([
                'branch_id' => $data['branch_id'] ?? session('current_branch_id'),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $data['customer_phone'],
                'email' => $data['customer_email'],
                'document_type' => DocumentType::CC,
                'customer_type' => \App\Enums\CustomerType::NATURAL,
                'is_active' => true,
            ]);

            return $customer->id;
        }

        return null;
    }

    /**
     * Send reminder notifications
     */
    public function sendReminders(): int
    {
        $count = 0;

        $reservations = Reservation::whereNull('reminder_sent_at')
            ->whereIn('status', [ReservationStatus::PENDING->value, ReservationStatus::CONFIRMED->value])
            ->whereDate('reservation_date', now()->addDay()->toDateString())
            ->get();

        foreach ($reservations as $reservation) {
            // TODO: Implement notification sending (SMS, Email, WhatsApp)
            // $this->sendReminderNotification($reservation);

            $reservation->update(['reminder_sent_at' => now()]);
            $count++;
        }

        return $count;
    }

    /**
     * Mark overdue reservations as no-show
     */
    public function processNoShows(int $graceMinutes = 30): int
    {
        $count = 0;

        $reservations = Reservation::whereIn('status', [
                ReservationStatus::PENDING->value,
                ReservationStatus::CONFIRMED->value,
            ])
            ->whereDate('reservation_date', now()->toDateString())
            ->get()
            ->filter(function ($reservation) use ($graceMinutes) {
                return $reservation->datetime->addMinutes($graceMinutes)->isPast();
            });

        foreach ($reservations as $reservation) {
            $reservation->markNoShow();
            $count++;
        }

        return $count;
    }
}
