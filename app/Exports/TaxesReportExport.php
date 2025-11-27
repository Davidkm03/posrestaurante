<?php

namespace App\Exports;

use App\Models\Order;
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

class TaxesReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize, WithColumnFormatting
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
        // Agrupar por día para el reporte de impuestos
        return Order::select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(subtotal) as total_subtotal'),
                DB::raw('SUM(discount) as total_discounts'),
                DB::raw('SUM(tax) as total_tax'),
                DB::raw('SUM(total) as total_sales'),
            ])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->when($this->branchId, fn($q) => $q->where('branch_id', $this->branchId))
            ->whereIn('status', ['completed', 'delivered', 'paid'])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Nº Órdenes',
            'Subtotal',
            'Descuentos',
            'IVA Recaudado',
            'Base Gravable',
            'Total Ventas',
        ];
    }

    public function map($row): array
    {
        $baseGravable = $row->total_subtotal - $row->total_discounts;
        
        return [
            Carbon::parse($row->date)->format('d/m/Y'),
            $row->total_orders,
            $row->total_subtotal,
            $row->total_discounts,
            $row->total_tax,
            $baseGravable,
            $row->total_sales,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DC2626'],
                ],
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER,
            'C' => NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
            'D' => NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
            'E' => NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
            'F' => NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
            'G' => NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
        ];
    }

    public function title(): string
    {
        return 'Reporte de Impuestos';
    }
}
