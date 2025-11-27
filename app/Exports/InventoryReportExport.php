<?php

namespace App\Exports;

use App\Models\StockMovement;
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

class InventoryReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize, WithColumnFormatting
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
        return StockMovement::with(['product', 'user'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->when($this->branchId, fn($q) => $q->where('branch_id', $this->branchId))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Hora',
            'Producto',
            'Tipo de Movimiento',
            'Cantidad',
            'Stock Anterior',
            'Stock Después',
            'Costo Unitario',
            'Valor Total',
            'Referencia',
            'Usuario',
            'Notas',
        ];
    }

    public function map($movement): array
    {
        return [
            $movement->created_at->format('d/m/Y'),
            $movement->created_at->format('H:i'),
            $movement->product->name ?? 'N/A',
            $movement->type->label(),
            $movement->quantity,
            $movement->stock_before ?? 'N/A',
            $movement->stock_after ?? 'N/A',
            $movement->unit_cost ?? 0,
            ($movement->quantity * ($movement->unit_cost ?? 0)),
            $movement->reference ?? 'N/A',
            $movement->user->name ?? 'Sistema',
            $movement->notes ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '7C3AED'],
                ],
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_NUMBER,
            'H' => NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
            'I' => NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
        ];
    }

    public function title(): string
    {
        return 'Movimientos de Inventario';
    }
}
