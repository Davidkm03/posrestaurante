<?php

namespace App\Livewire\Reservations;

use App\Models\Reservation;
use App\Models\Table;
use App\Services\ReservationService;
use App\Enums\ReservationStatus;
use Livewire\Component;
use Carbon\Carbon;

class ReservationCalendar extends Component
{
    public string $currentDate;
    public string $viewMode = 'day'; // day, week, month
    public ?int $selectedReservationId = null;
    public bool $showCreateModal = false;
    public bool $showDetailModal = false;

    // Create form
    public string $customerName = '';
    public string $customerPhone = '';
    public string $customerEmail = '';
    public string $reservationDate = '';
    public string $reservationTime = '';
    public int $partySize = 2;
    public int $duration = 90;
    public ?int $tableId = null;
    public string $specialRequests = '';
    public string $internalNotes = '';

    protected ReservationService $reservationService;

    protected $rules = [
        'customerName' => 'required|string|max:255',
        'customerPhone' => 'required|string|max:20',
        'customerEmail' => 'nullable|email|max:255',
        'reservationDate' => 'required|date|after_or_equal:today',
        'reservationTime' => 'required',
        'partySize' => 'required|integer|min:1|max:50',
        'duration' => 'required|integer|min:30|max:480',
        'tableId' => 'nullable|exists:tables,id',
    ];

    protected $messages = [
        'customerName.required' => 'El nombre es obligatorio',
        'customerPhone.required' => 'El teléfono es obligatorio',
        'reservationDate.required' => 'La fecha es obligatoria',
        'reservationDate.after_or_equal' => 'La fecha debe ser hoy o posterior',
        'reservationTime.required' => 'La hora es obligatoria',
        'partySize.required' => 'El número de personas es obligatorio',
    ];

    public function boot(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    public function mount()
    {
        $this->currentDate = now()->toDateString();
        $this->reservationDate = now()->toDateString();
    }

    public function previousDay()
    {
        $this->currentDate = Carbon::parse($this->currentDate)->subDay()->toDateString();
    }

    public function nextDay()
    {
        $this->currentDate = Carbon::parse($this->currentDate)->addDay()->toDateString();
    }

    public function previousWeek()
    {
        $this->currentDate = Carbon::parse($this->currentDate)->subWeek()->toDateString();
    }

    public function nextWeek()
    {
        $this->currentDate = Carbon::parse($this->currentDate)->addWeek()->toDateString();
    }

    public function goToToday()
    {
        $this->currentDate = now()->toDateString();
    }

    public function setViewMode(string $mode)
    {
        $this->viewMode = $mode;
    }

    public function openCreateModal(?string $date = null, ?string $time = null)
    {
        $this->resetCreateForm();
        $this->reservationDate = $date ?? $this->currentDate;
        $this->reservationTime = $time ?? '19:00';
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetCreateForm();
    }

    public function resetCreateForm()
    {
        $this->customerName = '';
        $this->customerPhone = '';
        $this->customerEmail = '';
        $this->reservationDate = $this->currentDate;
        $this->reservationTime = '19:00';
        $this->partySize = 2;
        $this->duration = 90;
        $this->tableId = null;
        $this->specialRequests = '';
        $this->internalNotes = '';
    }

    public function createReservation()
    {
        $this->validate();

        try {
            $reservation = $this->reservationService->create([
                'customer_name' => $this->customerName,
                'customer_phone' => $this->customerPhone,
                'customer_email' => $this->customerEmail,
                'reservation_date' => $this->reservationDate,
                'reservation_time' => $this->reservationTime,
                'party_size' => $this->partySize,
                'duration_minutes' => $this->duration,
                'table_id' => $this->tableId,
                'special_requests' => $this->specialRequests,
                'internal_notes' => $this->internalNotes,
            ]);

            $this->dispatch('notify', type: 'success', message: 'Reservación creada exitosamente');
            $this->closeCreateModal();

        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function selectReservation(int $id)
    {
        $this->selectedReservationId = $id;
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedReservationId = null;
    }

    public function confirmReservation()
    {
        $reservation = Reservation::find($this->selectedReservationId);
        if (!$reservation) return;

        try {
            $this->reservationService->confirm($reservation);
            $this->dispatch('notify', type: 'success', message: 'Reservación confirmada');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function cancelReservation()
    {
        $reservation = Reservation::find($this->selectedReservationId);
        if (!$reservation) return;

        try {
            $this->reservationService->cancel($reservation);
            $this->dispatch('notify', type: 'info', message: 'Reservación cancelada');
            $this->closeDetailModal();
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function seatReservation()
    {
        $reservation = Reservation::find($this->selectedReservationId);
        if (!$reservation) return;

        try {
            $this->reservationService->seat($reservation);
            $this->dispatch('notify', type: 'success', message: 'Cliente sentado');
            $this->dispatch('table-updated');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function markNoShow()
    {
        $reservation = Reservation::find($this->selectedReservationId);
        if (!$reservation) return;

        try {
            $this->reservationService->markNoShow($reservation);
            $this->dispatch('notify', type: 'warning', message: 'Marcado como no asistió');
            $this->closeDetailModal();
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function getReservations()
    {
        $branchId = session('current_branch_id');
        $date = Carbon::parse($this->currentDate);

        if ($this->viewMode === 'week') {
            $startDate = $date->copy()->startOfWeek();
            $endDate = $date->copy()->endOfWeek();

            return Reservation::forBranch($branchId)
                ->forDateRange($startDate, $endDate)
                ->with(['table', 'customer'])
                ->orderBy('reservation_date')
                ->orderBy('reservation_time')
                ->get()
                ->groupBy(fn($r) => $r->reservation_date->format('Y-m-d'));
        }

        return Reservation::forBranch($branchId)
            ->forDate($this->currentDate)
            ->with(['table', 'customer'])
            ->orderBy('reservation_time')
            ->get();
    }

    public function getAvailableTables()
    {
        if (!$this->reservationDate || !$this->reservationTime) {
            return collect();
        }

        $branchId = session('current_branch_id');

        return $this->reservationService->getAvailableTables(
            $branchId,
            $this->reservationDate,
            $this->reservationTime,
            $this->partySize,
            $this->duration
        );
    }

    public function getTimeSlots()
    {
        $slots = [];
        $start = Carbon::parse('11:00');
        $end = Carbon::parse('22:00');

        while ($start <= $end) {
            $slots[] = $start->format('H:i');
            $start->addMinutes(30);
        }

        return $slots;
    }

    public function render()
    {
        $branchId = session('current_branch_id');
        $selectedReservation = $this->selectedReservationId
            ? Reservation::with(['table', 'customer'])->find($this->selectedReservationId)
            : null;

        return view('livewire.reservations.reservation-calendar', [
            'reservations' => $this->getReservations(),
            'selectedReservation' => $selectedReservation,
            'availableTables' => $this->getAvailableTables(),
            'timeSlots' => $this->getTimeSlots(),
            'stats' => $this->reservationService->getStats($branchId),
        ]);
    }
}
