<?php

namespace App\Services;

use App\Exports\SalesReportExport;
use App\Exports\ProductsReportExport;
use App\Exports\InventoryReportExport;
use App\Exports\TaxesReportExport;
use App\Models\Order;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExportService
{
    /**
     * Exporta reporte de ventas a Excel
     */
    public function salesToExcel(Carbon $startDate, Carbon $endDate, ?int $branchId = null)
    {
        $filename = 'ventas_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.xlsx';
        
        return Excel::download(
            new SalesReportExport($startDate, $endDate, $branchId),
            $filename
        );
    }

    /**
     * Exporta reporte de ventas a PDF
     */
    public function salesToPdf(Carbon $startDate, Carbon $endDate, ?int $branchId = null)
    {
        $orders = Order::with(['table', 'waiter', 'payments.paymentMethod', 'branch'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereIn('status', ['completed', 'delivered', 'paid'])
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total_orders' => $orders->count(),
            'total_sales' => $orders->sum('total'),
            'total_tax' => $orders->sum('tax'),
            'total_discount' => $orders->sum('discount'),
            'average_ticket' => $orders->count() > 0 ? $orders->sum('total') / $orders->count() : 0,
        ];

        $pdf = Pdf::loadView('exports.pdf.sales', [
            'orders' => $orders,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'generatedAt' => now(),
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('ventas_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.pdf');
    }

    /**
     * Exporta reporte de productos a Excel
     */
    public function productsToExcel(Carbon $startDate, Carbon $endDate, ?int $branchId = null)
    {
        $filename = 'productos_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.xlsx';
        
        return Excel::download(
            new ProductsReportExport($startDate, $endDate, $branchId),
            $filename
        );
    }

    /**
     * Exporta reporte de productos a PDF
     */
    public function productsToPdf(Carbon $startDate, Carbon $endDate, ?int $branchId = null)
    {
        $products = Product::select([
                'products.*',
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_quantity'),
                DB::raw('COALESCE(SUM(order_items.total), 0) as total_sales'),
            ])
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', function($join) use ($startDate, $endDate, $branchId) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->whereBetween('orders.created_at', [$startDate, $endDate])
                    ->whereIn('orders.status', ['completed', 'delivered', 'paid']);
                
                if ($branchId) {
                    $join->where('orders.branch_id', $branchId);
                }
            })
            ->with('category')
            ->groupBy('products.id')
            ->orderByDesc('total_sales')
            ->get();

        $summary = [
            'total_products' => $products->count(),
            'products_sold' => $products->where('total_quantity', '>', 0)->count(),
            'total_revenue' => $products->sum('total_sales'),
            'total_quantity' => $products->sum('total_quantity'),
        ];

        $pdf = Pdf::loadView('exports.pdf.products', [
            'products' => $products,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'generatedAt' => now(),
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('productos_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.pdf');
    }

    /**
     * Exporta reporte de inventario a Excel
     */
    public function inventoryToExcel(Carbon $startDate, Carbon $endDate, ?int $branchId = null)
    {
        $filename = 'inventario_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.xlsx';
        
        return Excel::download(
            new InventoryReportExport($startDate, $endDate, $branchId),
            $filename
        );
    }

    /**
     * Exporta reporte de impuestos a Excel
     */
    public function taxesToExcel(Carbon $startDate, Carbon $endDate, ?int $branchId = null)
    {
        $filename = 'impuestos_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.xlsx';
        
        return Excel::download(
            new TaxesReportExport($startDate, $endDate, $branchId),
            $filename
        );
    }

    /**
     * Exporta reporte de impuestos a PDF
     */
    public function taxesToPdf(Carbon $startDate, Carbon $endDate, ?int $branchId = null)
    {
        $data = Order::select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(subtotal) as total_subtotal'),
                DB::raw('SUM(discount) as total_discounts'),
                DB::raw('SUM(tax) as total_tax'),
                DB::raw('SUM(total) as total_sales'),
            ])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereIn('status', ['completed', 'delivered', 'paid'])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        $summary = [
            'total_days' => $data->count(),
            'total_orders' => $data->sum('total_orders'),
            'total_subtotal' => $data->sum('total_subtotal'),
            'total_discounts' => $data->sum('total_discounts'),
            'total_tax' => $data->sum('total_tax'),
            'total_sales' => $data->sum('total_sales'),
        ];

        $pdf = Pdf::loadView('exports.pdf.taxes', [
            'data' => $data,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'generatedAt' => now(),
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('impuestos_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.pdf');
    }

    /**
     * Exporta cierre de caja a PDF
     */
    public function cashCloseToPdf($cashSession)
    {
        $cashSession->load(['cashRegister', 'user']);
        
        $pdf = Pdf::loadView('exports.pdf.cash-close', [
            'session' => $cashSession,
            'generatedAt' => now(),
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('cierre_caja_' . $cashSession->id . '_' . now()->format('Ymd') . '.pdf');
    }
}
