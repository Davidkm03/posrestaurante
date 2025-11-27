<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashSession;
use App\Models\CashMovement;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CashController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');

        // Sesión actual
        $currentSession = CashSession::whereHas('cashRegister', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereNull('closed_at')
            ->with(['cashRegister', 'user', 'movements'])
            ->first();

        // Historial de sesiones
        $query = CashSession::whereHas('cashRegister', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->with(['cashRegister', 'user', 'closedBy']);

        if ($request->filled('date_from')) {
            $query->whereDate('opened_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('opened_at', '<=', $request->date_to);
        }

        $sessions = $query->latest('opened_at')->paginate(20);
        $cashRegisters = CashRegister::where('branch_id', $branchId)
            ->where('is_active', true)
            ->get();

        return view('admin.cash.index', compact('currentSession', 'sessions', 'cashRegisters'));
    }

    public function open(Request $request)
    {
        $branchId = session('current_branch_id');

        // Verificar si ya hay una sesión abierta
        $existingSession = CashSession::whereHas('cashRegister', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereNull('closed_at')
            ->first();

        if ($existingSession) {
            return back()->with('error', 'Ya existe una sesión de caja abierta.');
        }

        $request->validate([
            'cash_register_id' => 'required|exists:cash_registers,id',
            'opening_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $session = CashSession::create([
            'cash_register_id' => $request->cash_register_id,
            'user_id' => auth()->id(),
            'opened_at' => now(),
            'opening_amount' => $request->opening_amount,
            'opening_notes' => $request->notes,
        ]);

        // Guardar en sesión
        session(['cash_session_id' => $session->id]);

        return redirect()
            ->route('admin.cash.index')
            ->with('success', 'Caja abierta exitosamente.');
    }

    public function close(Request $request, CashSession $cashSession)
    {
        if ($cashSession->closed_at) {
            return back()->with('error', 'Esta sesión ya está cerrada.');
        }

        $request->validate([
            'closing_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        // Calcular totales
        $totalSales = $cashSession->orders()
            ->whereNotIn('status', ['cancelled', 'voided'])
            ->sum('total');

        $totalCash = $cashSession->payments()
            ->whereHas('paymentMethod', fn($q) => $q->where('type', 'cash'))
            ->sum('amount');

        $totalCard = $cashSession->payments()
            ->whereHas('paymentMethod', fn($q) => $q->whereIn('type', ['credit_card', 'debit_card']))
            ->sum('amount');

        $totalOther = $cashSession->payments()
            ->whereHas('paymentMethod', fn($q) => $q->whereNotIn('type', ['cash', 'credit_card', 'debit_card']))
            ->sum('amount');

        $movements = $cashSession->movements;
        $totalIn = $movements->where('type', 'in')->sum('amount');
        $totalOut = $movements->where('type', 'out')->sum('amount');

        $expectedCash = $cashSession->opening_amount + $totalCash + $totalIn - $totalOut;
        $difference = $request->closing_amount - $expectedCash;

        $cashSession->update([
            'closed_by' => auth()->id(),
            'closed_at' => now(),
            'closing_amount' => $request->closing_amount,
            'expected_amount' => $expectedCash,
            'difference' => $difference,
            'total_sales' => $totalSales,
            'total_cash' => $totalCash,
            'total_card' => $totalCard,
            'total_other' => $totalOther,
            'closing_notes' => $request->notes,
        ]);

        // Limpiar sesión
        session()->forget('cash_session_id');

        return redirect()
            ->route('admin.cash.index')
            ->with('success', 'Caja cerrada exitosamente.');
    }

    public function show(CashSession $cashSession)
    {
        $cashSession->load([
            'cashRegister',
            'user',
            'closedBy',
            'movements.user',
            'orders.payments.paymentMethod',
        ]);

        return view('admin.cash.show', compact('cashSession'));
    }

    public function addMovement(Request $request, CashSession $cashSession)
    {
        if ($cashSession->closed_at) {
            return back()->with('error', 'No se pueden agregar movimientos a una caja cerrada.');
        }

        $request->validate([
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        CashMovement::create([
            'cash_session_id' => $cashSession->id,
            'user_id' => auth()->id(),
            'type' => $request->type,
            'amount' => $request->amount,
            'reason' => $request->reason,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Movimiento registrado exitosamente.');
    }

    public function report(Request $request)
    {
        $branchId = session('current_branch_id');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $sessions = CashSession::whereHas('cashRegister', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereNotNull('closed_at')
            ->whereBetween('opened_at', [$startDate, $endDate . ' 23:59:59'])
            ->with(['user', 'closedBy', 'cashRegister'])
            ->get();

        $summary = [
            'total_sessions' => $sessions->count(),
            'total_sales' => $sessions->sum('total_sales'),
            'total_cash' => $sessions->sum('total_cash'),
            'total_card' => $sessions->sum('total_card'),
            'total_other' => $sessions->sum('total_other'),
            'total_difference' => $sessions->sum('difference'),
        ];

        return view('admin.cash.report', compact('sessions', 'summary', 'startDate', 'endDate'));
    }

    public function sessions(Request $request)
    {
        $branchId = session('current_branch_id');

        $query = CashSession::whereHas('cashRegister', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereNotNull('closed_at')
            ->with(['cashRegister', 'user', 'closedBy']);

        if ($request->filled('date_from')) {
            $query->whereDate('opened_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('opened_at', '<=', $request->date_to);
        }

        if ($request->filled('cash_register_id')) {
            $query->where('cash_register_id', $request->cash_register_id);
        }

        $sessions = $query->latest('opened_at')->paginate(20);
        $cashRegisters = CashRegister::where('branch_id', $branchId)->get();

        return view('admin.cash.sessions', compact('sessions', 'cashRegisters'));
    }

    public function showSession(CashSession $session)
    {
        $session->load([
            'cashRegister',
            'user',
            'closedBy',
            'movements.user',
            'orders.payments.paymentMethod',
        ]);

        $cashSession = $session;
        return view('admin.cash.show', compact('cashSession'));
    }

    public function sessionReport(CashSession $session)
    {
        $session->load([
            'cashRegister',
            'user',
            'closedBy',
            'movements.user',
            'orders.payments.paymentMethod',
        ]);

        // Aquí podrías generar un PDF o vista especial para imprimir
        return view('admin.cash.session-report', compact('session'));
    }
}
