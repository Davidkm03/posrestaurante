<?php

namespace App\Livewire\Reservations;

use App\Models\Reservation;
use App\Enums\ReservationStatus;
use App\Services\ReservationService;
use Livewire\Component;

class TodayReservationsWidget extends Component
{
    public function mount()
    {
        //
    }

    public function quickConfirm(int $reservationId)
    {
        $reservation = Reservation::find($reservationId);
        if ($reservation && $reservation->status === ReservationStatus::PENDING) {
            app(ReservationService::class)->confirm($reservation);
            $this->dispatch('notify', type: 'success', message: 'Reservación confirmada');
        }
    }

    public function quickSeat(int $reservationId)
    {
        $reservation = Reservation::find($reservationId);
        if ($reservation && $reservation->status === ReservationStatus::CONFIRMED) {
            app(ReservationService::class)->seat($reservation);
            $this->dispatch('notify', type: 'success', message: 'Cliente sentado');
        }
    }

    public function render()
    {
        $branchId = session('current_branch_id');

        $reservations = Reservation::forBranch($branchId)
            ->today()
            ->with('table')
            ->orderBy('reservation_time')
            ->get();

        $upcoming = $reservations->filter(fn($r) => in_array($r->status, [
            ReservationStatus::PENDING,
            ReservationStatus::CONFIRMED
        ]));

        $current = $reservations->where('status', ReservationStatus::SEATED);

        return view('livewire.reservations.today-reservations-widget', [
            'reservations' => $reservations,
            'upcoming' => $upcoming,
            'current' => $current,
            'totalToday' => $reservations->count(),
            'pendingCount' => $reservations->where('status', ReservationStatus::PENDING)->count(),
            'confirmedCount' => $reservations->where('status', ReservationStatus::CONFIRMED)->count(),
        ]);
    }
}
