<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct(
        protected ReservationService $reservationService
    ) {}

    public function index()
    {
        return view('admin.reservations.index');
    }

    public function calendar()
    {
        return view('admin.reservations.calendar');
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['table', 'customer', 'user', 'branch']);

        return view('admin.reservations.show', compact('reservation'));
    }

    public function upcoming()
    {
        $branchId = session('current_branch_id');
        $reservations = $this->reservationService->getUpcoming($branchId, 14);

        return view('admin.reservations.upcoming', compact('reservations'));
    }

    public function today()
    {
        $branchId = session('current_branch_id');
        $reservations = $this->reservationService->getTodayReservations($branchId);

        return view('admin.reservations.today', compact('reservations'));
    }

    public function stats(Request $request)
    {
        $branchId = session('current_branch_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $stats = $this->reservationService->getStats($branchId, $startDate, $endDate);

        return view('admin.reservations.stats', compact('stats', 'startDate', 'endDate'));
    }
}
