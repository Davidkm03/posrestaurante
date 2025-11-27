<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Category;
use App\Models\User;
use App\Enums\OrderStatus;
use App\Services\ExportService;
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

        // Formato para SQLite usando strftime
        $dateFormat = match($groupBy) {
            'hour' => "strftime('%Y-%m-%d %H:00', created_at)",
            'day' => "strftime('%Y-%m-%d', created_at)",
            'week' => "strftime('%Y-%W', created_at)",
            'month' => "strftime('%Y-%m', created_at)",
            default => "strftime('%Y-%m-%d', created_at)",
        };

        $sales = Order::where('branch_id', $branchId)
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->where('status', '!=', OrderStatus::CANCELLED->value)
            ->selectRaw("{$dateFormat} as period, COUNT(*) as orders, SUM(total) as total, SUM(subtotal) as subtotal, SUM(tax_amount) as tax, SUM(discount_amount) as discount")
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
            ->where('orders.status', '!=', OrderStatus::CANCELLED->value);

        if ($categoryId) {
            $query->where('products.category_id', $categoryId);
        }

        $products = $query
            ->selectRaw('products.id, products.name, categories.name as category_name, SUM(order_items.quantity) as quantity, SUM(order_items.total - order_items.tax_amount) as total')
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
            ->where('status', '!=', OrderStatus::CANCELLED->value)
            ->whereNotNull('user_id')
            ->with('waiter')
            ->selectRaw('user_id, COUNT(*) as orders, SUM(total) as total, AVG(total) as avg_ticket')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->get();

        return view('admin.reports.waiters', compact('waiters', 'startDate', 'endDate'));
    }

    public function taxes(Request $request)
    {
        $branchId = session('current_branch_id');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $taxData = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.branch_id', $branchId)
            ->whereBetween('orders.created_at', [$startDate, $endDate . ' 23:59:59'])
            ->where('orders.status', '!=', OrderStatus::CANCELLED->value)
            ->selectRaw('SUM(order_items.tax_amount) as total_tax, SUM(order_items.unit_price * order_items.quantity - order_items.discount_amount) as base')
            ->first();

        $summary = [
            'total_base' => $taxData->base ?? 0,
            'total_tax' => $taxData->total_tax ?? 0,
        ];

        // Crear un array simple para la vista
        $taxes = collect([
            [
                'tax_type' => 'IVA',
                'tax_percentage' => 19,
                'total_tax' => $taxData->total_tax ?? 0,
                'base' => $taxData->base ?? 0,
            ]
        ]);

        return view('admin.reports.taxes', compact('taxes', 'summary', 'startDate', 'endDate'));
    }

    public function hourly(Request $request)
    {
        $branchId = session('current_branch_id');
        $date = $request->get('date', Carbon::today()->toDateString());

        $hourlyData = Order::where('branch_id', $branchId)
            ->whereDate('created_at', $date)
            ->where('status', '!=', OrderStatus::CANCELLED->value)
            ->selectRaw("CAST(strftime('%H', created_at) AS INTEGER) as hour, COUNT(*) as orders, SUM(total) as total")
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

    public function export(Request $request, ExportService $exportService)
    {
        $type = $request->get('type', 'sales');
        $format = $request->get('format', 'xlsx');
        $branchId = session('current_branch_id');
        $startDate = Carbon::parse($request->get('start_date', Carbon::now()->startOfMonth()->toDateString()));
        $endDate = Carbon::parse($request->get('end_date', Carbon::now()->toDateString()));

        return match($type) {
            'sales' => $format === 'pdf' 
                ? $exportService->salesToPdf($startDate, $endDate, $branchId)
                : $exportService->salesToExcel($startDate, $endDate, $branchId),
            'products' => $format === 'pdf'
                ? $exportService->productsToPdf($startDate, $endDate, $branchId)
                : $exportService->productsToExcel($startDate, $endDate, $branchId),
            'inventory' => $exportService->inventoryToExcel($startDate, $endDate, $branchId),
            'taxes' => $format === 'pdf'
                ? $exportService->taxesToPdf($startDate, $endDate, $branchId)
                : $exportService->taxesToExcel($startDate, $endDate, $branchId),
            default => back()->with('error', 'Tipo de reporte no válido.')
        };
    }
}
