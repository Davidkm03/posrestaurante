<?php

namespace App\Services;

use App\Models\CashSession;
use App\Models\CashMovement;
use App\Models\CashRegister;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CashService
{
    public function openSession(int $branchId, int $cashRegisterId, float $openingAmount, ?string $notes = null): CashSession
    {
        // Check for existing open session
        $existingSession = CashSession::where('branch_id', $branchId)
            ->whereNull('closed_at')
            ->first();

        if ($existingSession) {
            throw new \RuntimeException('Ya existe una sesión de caja abierta');
        }

        return CashSession::create([
            'branch_id' => $branchId,
            'cash_register_id' => $cashRegisterId,
            'opened_by' => auth()->id(),
            'opened_at' => now(),
            'opening_amount' => $openingAmount,
            'notes' => $notes,
        ]);
    }

    public function closeSession(CashSession $session, float $closingAmount, ?string $notes = null): CashSession
    {
        if ($session->closed_at) {
            throw new \RuntimeException('Esta sesión ya está cerrada');
        }

        $totals = $this->calculateSessionTotals($session);

        $expectedCash = $session->opening_amount + $totals['cash'] + $totals['movements_in'] - $totals['movements_out'];
        $difference = $closingAmount - $expectedCash;

        $session->update([
            'closed_by' => auth()->id(),
            'closed_at' => now(),
            'closing_amount' => $closingAmount,
            'expected_amount' => $expectedCash,
            'difference' => $difference,
            'total_sales' => $totals['sales'],
            'total_cash' => $totals['cash'],
            'total_card' => $totals['card'],
            'total_other' => $totals['other'],
            'closing_notes' => $notes,
        ]);

        return $session->fresh();
    }

    public function addMovement(CashSession $session, string $type, float $amount, string $reason, ?string $notes = null): CashMovement
    {
        if ($session->closed_at) {
            throw new \RuntimeException('No se pueden agregar movimientos a una caja cerrada');
        }

        return CashMovement::create([
            'cash_session_id' => $session->id,
            'user_id' => auth()->id(),
            'type' => $type, // 'in' or 'out'
            'amount' => $amount,
            'reason' => $reason,
            'notes' => $notes,
        ]);
    }

    public function calculateSessionTotals(CashSession $session): array
    {
        $orders = $session->orders()
            ->whereNotIn('status', ['cancelled', 'voided'])
            ->get();

        $payments = DB::table('payments')
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->join('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id')
            ->where('orders.cash_session_id', $session->id)
            ->where('payments.status', PaymentStatus::COMPLETED->value)
            ->select('payment_methods.type', DB::raw('SUM(payments.amount) as total'))
            ->groupBy('payment_methods.type')
            ->get()
            ->keyBy('type');

        $movements = $session->movements;

        return [
            'sales' => $orders->sum('total'),
            'orders_count' => $orders->count(),
            'cash' => $payments->get('cash')?->total ?? 0,
            'card' => ($payments->get('credit_card')?->total ?? 0) + ($payments->get('debit_card')?->total ?? 0),
            'other' => $payments->whereNotIn('type', ['cash', 'credit_card', 'debit_card'])->sum('total'),
            'movements_in' => $movements->where('type', 'in')->sum('amount'),
            'movements_out' => $movements->where('type', 'out')->sum('amount'),
        ];
    }

    public function getCurrentSession(int $branchId): ?CashSession
    {
        return CashSession::where('branch_id', $branchId)
            ->whereNull('closed_at')
            ->with(['cashRegister', 'openedBy'])
            ->first();
    }

    public function getSessionReport(CashSession $session): array
    {
        $totals = $this->calculateSessionTotals($session);

        $ordersByType = $session->orders()
            ->whereNotIn('status', ['cancelled', 'voided'])
            ->selectRaw('type, COUNT(*) as count, SUM(total) as total')
            ->groupBy('type')
            ->get();

        $ordersByHour = $session->orders()
            ->whereNotIn('status', ['cancelled', 'voided'])
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count, SUM(total) as total')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return [
            'session' => $session,
            'totals' => $totals,
            'by_type' => $ordersByType,
            'by_hour' => $ordersByHour,
            'movements' => $session->movements()->with('user')->get(),
        ];
    }

    public function getDailyReport(int $branchId, string $date): array
    {
        $sessions = CashSession::where('branch_id', $branchId)
            ->whereDate('opened_at', $date)
            ->with(['openedBy', 'closedBy', 'cashRegister'])
            ->get();

        return [
            'date' => $date,
            'sessions_count' => $sessions->count(),
            'total_sales' => $sessions->sum('total_sales'),
            'total_cash' => $sessions->sum('total_cash'),
            'total_card' => $sessions->sum('total_card'),
            'total_other' => $sessions->sum('total_other'),
            'total_difference' => $sessions->sum('difference'),
            'sessions' => $sessions,
        ];
    }
}
