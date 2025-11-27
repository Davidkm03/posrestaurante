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

class SalesReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize, WithColumnFormatting
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
        return Order::with(['table', 'waiter', 'payments.paymentMethod'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->when($this->branchId, fn($q) => $q->where('branch_id', $this->branchId))
            ->whereIn('status', ['completed', 'delivered', 'paid'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nº Orden',
            'Fecha',
            'Hora',
            'Mesa',
            'Mesero',
            'Tipo',
            'Subtotal',
            'Descuento',
            'Impuestos',
            'Total',
            'Método de Pago',
            'Estado',
        ];
    }

    public function map($order): array
    {
        $paymentMethods = $order->payments->map(fn($p) => $p->paymentMethod->name ?? 'Efectivo')->implode(', ');
        
        return [
            $order->order_number,
            $order->created_at->format('d/m/Y'),
            $order->created_at->format('H:i'),
            $order->table->number ?? 'N/A',
            $order->waiter->name ?? 'N/A',
            $order->type->label(),
            $order->subtotal,
            $order->discount,
            $order->tax,
            $order->total,
            $paymentMethods ?: 'Pendiente',
            $order->status->label(),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB'],
                ],
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
            'H' => NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
            'I' => NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
            'J' => NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
        ];
    }

    public function title(): string
    {
        return 'Reporte de Ventas';
    }
}
