<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function sales(Request $request)
    {
        $branchId = session('current_branch_id');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $groupBy = $request->get('group_by', 'day');

        $dateFormat = match($groupBy) {
            'hour' => '%Y-%m-%d %H:00',
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $sales = Order::where('branch_id', $branchId)
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->whereNotIn('status', [OrderStatus::CANCELLED->value, OrderStatus::VOIDED->value])
            ->selectRaw("DATE_FORMAT(created_at, '{$dateFormat}') as period, COUNT(*) as orders, SUM(total) as total, SUM(subtotal) as subtotal, SUM(tax) as tax, SUM(discount) as discount")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $summary = [
            'total_orders' => $sales->sum('orders'),
            'total_sales' => $sales->sum('total'),
            'total_subtotal' => $sales->sum('subtotal'),
            'total_tax' => $sales->sum('tax'),
            'total_discount' => $sales->sum('discount'),
            'avg_ticket' => $sales->sum('orders') > 0 ? $sales->sum('total') / $sales->sum('orders') : 0,
        ];

        return view('admin.reports.sales', compact('sales', 'summary', 'startDate', 'endDate', 'groupBy'));
    }

    public function products(Request $request)
    {
        $branchId = session('current_branch_id');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $categoryId = $request->get('category_id');

        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.branch_id', $branchId)
            ->whereBetween('orders.created_at', [$startDate, $endDate . ' 23:59:59'])
            ->whereNotIn('orders.status', [OrderStatus::CANCELLED->value, OrderStatus::VOIDED->value]);

        if ($categoryId) {
            $query->where('products.category_id', $categoryId);
        }

        $products = $query
            ->selectRaw('products.id, products.name, categories.name as category_name, SUM(order_items.quantity) as quantity, SUM(order_items.subtotal) as total')
            ->groupBy('products.id', 'products.name', 'categories.name')
            ->orderByDesc('quantity')
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('admin.reports.products', compact('products', 'categories', 'startDate', 'endDate', 'categoryId'));
    }

    public function waiters(Request $request)
    {
        $branchId = session('current_branch_id');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $waiters = Order::where('branch_id', $branchId)
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->whereNotIn('status', [OrderStatus::CANCELLED->value, OrderStatus::VOIDED->value])
            ->whereNotNull('waiter_id')
            ->with('waiter')
            ->selectRaw('waiter_id, COUNT(*) as orders, SUM(total) as total, AVG(total) as avg_ticket')
            ->groupBy('waiter_id')
            ->orderByDesc('total')
            ->get();

        return view('admin.reports.waiters', compact('waiters', 'startDate', 'endDate'));
    }

    public function taxes(Request $request)
    {
        $branchId = session('current_branch_id');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $taxes = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.branch_id', $branchId)
            ->whereBetween('orders.created_at', [$startDate, $endDate . ' 23:59:59'])
            ->whereNotIn('orders.status', [OrderStatus::CANCELLED->value, OrderStatus::VOIDED->value])
            ->selectRaw('order_items.tax_type, order_items.tax_percentage, SUM(order_items.tax_amount) as total_tax, SUM(order_items.subtotal) as base')
            ->groupBy('order_items.tax_type', 'order_items.tax_percentage')
            ->orderBy('order_items.tax_type')
            ->get();

        $summary = [
            'total_base' => $taxes->sum('base'),
            'total_tax' => $taxes->sum('total_tax'),
        ];

        return view('admin.reports.taxes', compact('taxes', 'summary', 'startDate', 'endDate'));
    }

    public function hourly(Request $request)
    {
        $branchId = session('current_branch_id');
        $date = $request->get('date', Carbon::today()->toDateString());

        $hourlyData = Order::where('branch_id', $branchId)
            ->whereDate('created_at', $date)
            ->whereNotIn('status', [OrderStatus::CANCELLED->value, OrderStatus::VOIDED->value])
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as orders, SUM(total) as total')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');

        // Completar las 24 horas
        $hours = collect(range(0, 23))->mapWithKeys(function ($hour) use ($hourlyData) {
            return [$hour => $hourlyData->get($hour, (object)['hour' => $hour, 'orders' => 0, 'total' => 0])];
        });

        return view('admin.reports.hourly', compact('hours', 'date'));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'sales');
        $format = $request->get('format', 'xlsx');

        // Implementar exportación según el tipo y formato
        // Por ahora solo retornamos mensaje
        return back()->with('info', 'Función de exportación en desarrollo.');
    }
}
