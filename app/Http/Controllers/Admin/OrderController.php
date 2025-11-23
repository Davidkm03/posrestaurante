<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');

        $query = Order::where('branch_id', $branchId)
            ->with(['table', 'waiter', 'customer', 'items.product']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('waiter')) {
            $query->where('waiter_id', $request->waiter);
        }

        $orders = $query->latest()->paginate(20);

        $statuses = OrderStatus::cases();
        $types = OrderType::cases();

        return view('admin.orders.index', compact('orders', 'statuses', 'types'));
    }

    public function show(Order $order)
    {
        $order->load([
            'table.zone',
            'waiter',
            'customer',
            'cashier',
            'items.product.category',
            'items.modifiers',
            'payments.method',
            'invoice',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:' . implode(',', array_column(OrderStatus::cases(), 'value')),
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        // Eventos según el estado
        if ($request->status === OrderStatus::DELIVERED->value) {
            $order->update(['delivered_at' => now()]);
        }

        return back()->with('success', 'Estado de la orden actualizado.');
    }

    public function cancel(Request $request, Order $order)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if (!$order->canBeCancelled()) {
            return back()->with('error', 'Esta orden no puede ser cancelada.');
        }

        $order->update([
            'status' => OrderStatus::CANCELLED->value,
            'cancellation_reason' => $request->reason,
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id(),
        ]);

        return back()->with('success', 'Orden cancelada exitosamente.');
    }

    public function print(Order $order)
    {
        $order->load([
            'table.zone',
            'waiter',
            'customer',
            'items.product',
            'items.modifiers',
            'branch',
        ]);

        return view('admin.orders.print', compact('order'));
    }

    public function printKitchen(Order $order)
    {
        $order->load([
            'table',
            'items.product.category',
            'items.modifiers',
        ]);

        return view('admin.orders.print-kitchen', compact('order'));
    }

    public function dailyReport(Request $request)
    {
        $branchId = session('current_branch_id');
        $date = $request->get('date', Carbon::today()->toDateString());

        $orders = Order::where('branch_id', $branchId)
            ->whereDate('created_at', $date)
            ->with(['items.product', 'payments.method', 'waiter'])
            ->get();

        $summary = [
            'total_orders' => $orders->count(),
            'total_sales' => $orders->whereNotIn('status', [OrderStatus::CANCELLED->value, OrderStatus::VOIDED->value])->sum('total'),
            'cancelled_orders' => $orders->where('status', OrderStatus::CANCELLED->value)->count(),
            'avg_ticket' => $orders->whereNotIn('status', [OrderStatus::CANCELLED->value, OrderStatus::VOIDED->value])->avg('total') ?? 0,
            'by_type' => $orders->groupBy('type')->map->count(),
            'by_status' => $orders->groupBy('status')->map->count(),
            'by_payment' => $orders->flatMap->payments->groupBy('payment_method_id')->map->sum('amount'),
        ];

        return view('admin.orders.daily-report', compact('orders', 'summary', 'date'));
    }
}
