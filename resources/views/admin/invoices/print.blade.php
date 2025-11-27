<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $invoice->getFullNumber() }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }
        .invoice {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            color: #666;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .invoice-info .left,
        .invoice-info .right {
            width: 48%;
        }
        .invoice-info h3 {
            font-size: 14px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .invoice-info p {
            margin-bottom: 3px;
        }
        .invoice-info .label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }
        .invoice-number {
            background: #f5f5f5;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .invoice-number h2 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .invoice-number .status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-voided { background: #e5e7eb; color: #374151; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th,
        table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #f9fafb;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        table td.right,
        table th.right {
            text-align: right;
        }
        table td.center,
        table th.center {
            text-align: center;
        }
        .totals {
            width: 300px;
            margin-left: auto;
        }
        .totals table {
            margin-bottom: 0;
        }
        .totals td {
            padding: 5px 8px;
        }
        .totals .total-row {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #333;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .cufe {
            margin-top: 15px;
            padding: 10px;
            background: #f9fafb;
            border-radius: 5px;
            font-family: monospace;
            font-size: 9px;
            word-break: break-all;
        }
        .cufe-label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .print-btn:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">Imprimir</button>

    <div class="invoice">
        <div class="header">
            <h1>{{ $invoice->branch->name ?? 'Restaurante' }}</h1>
            <p>NIT: {{ $invoice->branch->nit ?? 'N/A' }}</p>
            <p>{{ $invoice->branch->address ?? '' }} - {{ $invoice->branch->city ?? '' }}</p>
            <p>Tel: {{ $invoice->branch->phone ?? '' }}</p>
        </div>

        <div class="invoice-number">
            <h2>FACTURA ELECTRÓNICA DE VENTA</h2>
            <p style="font-size: 20px; font-weight: bold;">{{ $invoice->getFullNumber() }}</p>
            <span class="status status-{{ $invoice->status->value }}">
                {{ $invoice->status->label() }}
            </span>
        </div>

        <div class="invoice-info">
            <div class="left">
                <h3>Datos del Cliente</h3>
                @if($invoice->customer)
                    <p><span class="label">Nombre:</span> {{ $invoice->customer->full_name }}</p>
                    <p><span class="label">Documento:</span> {{ $invoice->customer->document_type }} {{ $invoice->customer->document_number }}</p>
                    @if($invoice->customer->address)
                        <p><span class="label">Dirección:</span> {{ $invoice->customer->address }}</p>
                    @endif
                    @if($invoice->customer->phone)
                        <p><span class="label">Teléfono:</span> {{ $invoice->customer->phone }}</p>
                    @endif
                    @if($invoice->customer->email)
                        <p><span class="label">Email:</span> {{ $invoice->customer->email }}</p>
                    @endif
                @else
                    <p>Consumidor Final</p>
                @endif
            </div>
            <div class="right">
                <h3>Datos de la Factura</h3>
                <p><span class="label">Fecha:</span> {{ $invoice->issue_date?->format('d/m/Y') ?? $invoice->issue_date }}</p>
                <p><span class="label">Hora:</span> {{ $invoice->issue_time }}</p>
                <p><span class="label">Vencimiento:</span> {{ $invoice->due_date?->format('d/m/Y') ?? $invoice->due_date }}</p>
                <p><span class="label">Forma de Pago:</span> {{ $invoice->payment_form == '1' ? 'Contado' : 'Crédito' }}</p>
                @if($invoice->order)
                    <p><span class="label">Orden:</span> #{{ $invoice->order->id }}</p>
                @endif
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th class="center">Cant.</th>
                    <th class="right">Precio Unit.</th>
                    <th class="right">IVA %</th>
                    <th class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->lines as $line)
                    <tr>
                        <td>{{ $line->code ?? '-' }}</td>
                        <td>{{ $line->description }}</td>
                        <td class="center">{{ number_format($line->quantity, 0) }}</td>
                        <td class="right">${{ number_format($line->unit_price, 0, ',', '.') }}</td>
                        <td class="right">{{ $line->tax_percentage > 0 ? number_format($line->tax_percentage, 0) . '%' : '-' }}</td>
                        <td class="right">${{ number_format($line->line_total, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    @if($invoice->order && $invoice->order->items)
                        @foreach($invoice->order->items as $item)
                            <tr>
                                <td>{{ $item->product->sku ?? $item->product_id }}</td>
                                <td>{{ $item->product_name }}</td>
                                <td class="center">{{ number_format($item->quantity, 0) }}</td>
                                <td class="right">${{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="right">{{ $item->tax_percentage > 0 ? number_format($item->tax_percentage, 0) . '%' : '-' }}</td>
                                <td class="right">${{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforelse
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td class="right">${{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                </tr>
                @if($invoice->total_discount > 0)
                    <tr>
                        <td>Descuento:</td>
                        <td class="right">-${{ number_format($invoice->total_discount, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if($invoice->total_tax_iva > 0)
                    <tr>
                        <td>IVA:</td>
                        <td class="right">${{ number_format($invoice->total_tax_iva, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if($invoice->total_tax_inc > 0)
                    <tr>
                        <td>Impoconsumo:</td>
                        <td class="right">${{ number_format($invoice->total_tax_inc, 0, ',', '.') }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td>TOTAL:</td>
                    <td class="right">${{ number_format($invoice->total, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        @if($invoice->payments && $invoice->payments->count() > 0)
            <div style="margin-top: 20px;">
                <h3 style="font-size: 14px; margin-bottom: 10px;">Pagos Recibidos</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Método</th>
                            <th>Referencia</th>
                            <th class="right">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->payments as $payment)
                            <tr>
                                <td>{{ $payment->paymentMethod->name ?? 'N/A' }}</td>
                                <td>{{ $payment->reference ?? '-' }}</td>
                                <td class="right">${{ number_format($payment->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if($invoice->cufe || $invoice->dian_cufe)
            <div class="cufe">
                <div class="cufe-label">CUFE (Código Único de Facturación Electrónica):</div>
                {{ $invoice->dian_cufe ?? $invoice->cufe }}
                @if($invoice->dian_status === 'SIMULADO')
                    <div style="margin-top: 5px; color: #92400e; font-weight: bold;">
                        * DOCUMENTO DE PRUEBA - NO VÁLIDO PARA EFECTOS FISCALES
                    </div>
                @endif
            </div>
        @endif

        @if($invoice->dian_uuid)
            <div style="margin-top: 10px; text-align: center;">
                <p style="font-size: 10px; color: #666;">UUID: {{ $invoice->dian_uuid }}</p>
            </div>
        @endif

        @if($invoice->resolution)
            <div class="footer">
                <p><strong>Resolución DIAN:</strong> {{ $invoice->resolution->resolution_number }}</p>
                <p>Prefijo: {{ $invoice->resolution->prefix }} | Rango: {{ $invoice->resolution->range_from }} - {{ $invoice->resolution->range_to }}</p>
                <p>Vigencia: {{ $invoice->resolution->valid_from?->format('d/m/Y') ?? $invoice->resolution->valid_from }} - {{ $invoice->resolution->valid_to?->format('d/m/Y') ?? $invoice->resolution->valid_to }}</p>
            </div>
        @endif

        @if($invoice->notes)
            <div style="margin-top: 20px; padding: 10px; background: #f9fafb; border-radius: 5px;">
                <strong>Notas:</strong> {{ $invoice->notes }}
            </div>
        @endif
    </div>

    <script>
        // Auto-print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
