<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\OrderItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductsReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize, WithColumnFormatting
{
    protected Carbon $startDate;
    protected Carbon $endDate;
    protected ?int $branchId;

    public function __construct(Carbon $startDate, Carbon $endDate, ?int $branchId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->branchId = $branchId;
    }

    public function collection()
    {
        return Product::select([
                'products.*',
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_quantity'),
                DB::raw('COALESCE(SUM(order_items.total), 0) as total_sales'),
            ])
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', function($join) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->whereBetween('orders.created_at', [$this->startDate, $this->endDate])
                    ->whereIn('orders.status', ['completed', 'delivered', 'paid']);
                
                if ($this->branchId) {
                    $join->where('orders.branch_id', $this->branchId);
                }
            })
            ->groupBy('products.id')
            ->orderByDesc('total_sales')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Código',
            'Producto',
            'Categoría',
            'Precio',
            'Costo',
            'Cantidad Vendida',
            'Total Ventas',
            'Margen',
            'Stock Actual',
            'Estado',
        ];
    }

    public function map($product): array
    {
        $margin = $product->total_sales > 0 
            ? (($product->total_sales - ($product->cost * $product->total_quantity)) / $product->total_sales) * 100 
            : 0;

        return [
            $product->sku ?? 'N/A',
            $product->name,
            $product->category->name ?? 'Sin categoría',
            $product->price,
            $product->cost ?? 0,
            $product->total_quantity,
            $product->total_sales,
            round($margin, 1) . '%',
            $product->track_inventory ? ($product->stock ?? 0) : 'N/A',
            $product->is_active ? 'Activo' : 'Inactivo',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '059669'],
                ],
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
            'E' => NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
            'F' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
        ];
    }

    public function title(): string
    {
        return 'Reporte de Productos';
    }
}
