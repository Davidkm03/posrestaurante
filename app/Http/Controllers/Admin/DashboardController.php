<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\CashSession;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');
        $today = Carbon::today();

        // Ventas del día
        $todaySales = Order::where('branch_id', $branchId)
            ->whereDate('created_at', $today)
            ->whereNotIn('status', [OrderStatus::CANCELLED->value, OrderStatus::VOIDED->value])
            ->sum('total');

        // Órdenes del día
        $todayOrders = Order::where('branch_id', $branchId)
            ->whereDate('created_at', $today)
            ->count();

        // Órdenes pendientes
        $pendingOrders = Order::where('branch_id', $branchId)
            ->whereIn('status', [OrderStatus::PENDING->value, OrderStatus::IN_PREPARATION->value])
            ->count();

        // Ticket promedio
        $avgTicket = Order::where('branch_id', $branchId)
            ->whereDate('created_at', $today)
            ->whereNotIn('status', [OrderStatus::CANCELLED->value, OrderStatus::VOIDED->value])
            ->avg('total') ?? 0;

        // Caja actual
        $currentCashSession = CashSession::where('branch_id', $branchId)
            ->whereNull('closed_at')
            ->first();

        // Ventas por hora (últimas 12 horas)
        $salesByHour = Order::where('branch_id', $branchId)
            ->whereDate('created_at', $today)
            ->whereNotIn('status', [OrderStatus::CANCELLED->value, OrderStatus::VOIDED->value])
            ->selectRaw('HOUR(created_at) as hour, SUM(total) as total, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');

        // Productos más vendidos
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.branch_id', $branchId)
            ->whereDate('orders.created_at', $today)
            ->whereNotIn('orders.status', [OrderStatus::CANCELLED->value, OrderStatus::VOIDED->value])
            ->selectRaw('products.name, SUM(order_items.quantity) as quantity, SUM(order_items.subtotal) as total')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('quantity')
            ->limit(5)
            ->get();

        // Últimas órdenes
        $recentOrders = Order::where('branch_id', $branchId)
            ->with(['table', 'waiter'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard.index', compact(
            'todaySales',
            'todayOrders',
            'pendingOrders',
            'avgTicket',
            'currentCashSession',
            'salesByHour',
            'topProducts',
            'recentOrders'
        ));
    }

    public function salesChart(Request $request)
    {
        $branchId = session('current_branch_id');
        $period = $request->get('period', 'week');

        $startDate = match($period) {
            'today' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfWeek(),
        };

        $sales = Order::where('branch_id', $branchId)
            ->where('created_at', '>=', $startDate)
            ->whereNotIn('status', [OrderStatus::CANCELLED->value, OrderStatus::VOIDED->value])
            ->selectRaw('DATE(created_at) as date, SUM(total) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($sales);
    }
}
