<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\ReservationService;
use App\Enums\ReservationStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReservationController extends Controller
{
    public function __construct(
        protected ReservationService $reservationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $branchId = $request->user()->currentBranchId();

        $query = Reservation::forBranch($branchId)
            ->with(['table', 'customer']);

        if ($request->filled('date')) {
            $query->forDate($request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->forDateRange($request->from_date, $request->to_date);
        }

        $reservations = $query->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $reservations,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required',
            'party_size' => 'required|integer|min:1|max:50',
            'duration_minutes' => 'nullable|integer|min:30|max:480',
            'table_id' => 'nullable|exists:tables,id',
            'special_requests' => 'nullable|string|max:500',
            'internal_notes' => 'nullable|string|max:500',
            'deposit_amount' => 'nullable|numeric|min:0',
        ]);

        $validated['branch_id'] = $request->user()->currentBranchId();

        try {
            $reservation = $this->reservationService->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Reservación creada exitosamente',
                'data' => $reservation->load(['table', 'customer']),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(Reservation $reservation): JsonResponse
    {
        $reservation->load(['table', 'customer', 'user']);

        return response()->json([
            'success' => true,
            'data' => $reservation,
        ]);
    }

    public function update(Request $request, Reservation $reservation): JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => 'sometimes|string|max:255',
            'customer_phone' => 'sometimes|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'reservation_date' => 'sometimes|date',
            'reservation_time' => 'sometimes',
            'party_size' => 'sometimes|integer|min:1|max:50',
            'duration_minutes' => 'sometimes|integer|min:30|max:480',
            'table_id' => 'nullable|exists:tables,id',
            'special_requests' => 'nullable|string|max:500',
            'internal_notes' => 'nullable|string|max:500',
        ]);

        try {
            $reservation = $this->reservationService->update($reservation, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Reservación actualizada',
                'data' => $reservation->load(['table', 'customer']),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function confirm(Reservation $reservation): JsonResponse
    {
        try {
            $this->reservationService->confirm($reservation);

            return response()->json([
                'success' => true,
                'message' => 'Reservación confirmada',
                'data' => $reservation->fresh(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function cancel(Request $request, Reservation $reservation): JsonResponse
    {
        try {
            $this->reservationService->cancel($reservation, $request->get('reason'));

            return response()->json([
                'success' => true,
                'message' => 'Reservación cancelada',
                'data' => $reservation->fresh(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function seat(Reservation $reservation): JsonResponse
    {
        try {
            $this->reservationService->seat($reservation);

            return response()->json([
                'success' => true,
                'message' => 'Cliente sentado',
                'data' => $reservation->fresh(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function noShow(Reservation $reservation): JsonResponse
    {
        try {
            $this->reservationService->markNoShow($reservation);

            return response()->json([
                'success' => true,
                'message' => 'Marcado como no asistió',
                'data' => $reservation->fresh(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function availableSlots(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'party_size' => 'required|integer|min:1',
            'duration' => 'nullable|integer|min:30',
        ]);

        $branchId = $request->user()->currentBranchId();

        $slots = $this->reservationService->getAvailableSlots(
            $branchId,
            $request->date,
            $request->party_size,
            $request->get('duration', 90)
        );

        return response()->json([
            'success' => true,
            'data' => $slots,
        ]);
    }

    public function availableTables(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required',
            'party_size' => 'required|integer|min:1',
            'duration' => 'nullable|integer|min:30',
        ]);

        $branchId = $request->user()->currentBranchId();

        $tables = $this->reservationService->getAvailableTables(
            $branchId,
            $request->date,
            $request->time,
            $request->party_size,
            $request->get('duration', 90)
        );

        return response()->json([
            'success' => true,
            'data' => $tables,
        ]);
    }

    public function today(Request $request): JsonResponse
    {
        $branchId = $request->user()->currentBranchId();
        $reservations = $this->reservationService->getTodayReservations($branchId);

        return response()->json([
            'success' => true,
            'data' => $reservations,
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $branchId = $request->user()->currentBranchId();
        $stats = $this->reservationService->getStats(
            $branchId,
            $request->get('start_date'),
            $request->get('end_date')
        );

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
