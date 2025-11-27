<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #1f2937;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2563eb;
        }
        
        .header h1 {
            font-size: 18px;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 11px;
            color: #6b7280;
        }
        
        .summary {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            background: #f3f4f6;
            padding: 10px;
            border-radius: 4px;
        }
        
        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 5px;
        }
        
        .summary-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
        }
        
        .summary-item .label {
            font-size: 9px;
            color: #6b7280;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th {
            background: #2563eb;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
        }
        
        td {
            padding: 6px 5px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
        }
        
        tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 8px;
            color: #9ca3af;
            text-align: center;
        }
        
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .badge-success {
            background: #dcfce7;
            color: #166534;
        }
        
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        
        .money {
            font-family: 'DejaVu Sans Mono', monospace;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 REPORTE DE VENTAS</h1>
        <p>Período: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="value">{{ number_format($summary['total_orders']) }}</div>
            <div class="label">Total Órdenes</div>
        </div>
        <div class="summary-item">
            <div class="value money">${{ number_format($summary['total_sales'], 0, ',', '.') }}</div>
            <div class="label">Total Ventas</div>
        </div>
        <div class="summary-item">
            <div class="value money">${{ number_format($summary['total_tax'], 0, ',', '.') }}</div>
            <div class="label">IVA Recaudado</div>
        </div>
        <div class="summary-item">
            <div class="value money">${{ number_format($summary['total_discount'], 0, ',', '.') }}</div>
            <div class="label">Descuentos</div>
        </div>
        <div class="summary-item">
            <div class="value money">${{ number_format($summary['average_ticket'], 0, ',', '.') }}</div>
            <div class="label">Ticket Promedio</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nº Orden</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Mesa</th>
                <th>Mesero</th>
                <th>Tipo</th>
                <th class="text-right">Subtotal</th>
                <th class="text-right">Desc.</th>
                <th class="text-right">IVA</th>
                <th class="text-right">Total</th>
                <th>Pago</th>
                <th class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                <td>{{ $order->created_at->format('H:i') }}</td>
                <td>{{ $order->table->number ?? 'N/A' }}</td>
                <td>{{ $order->waiter->name ?? 'N/A' }}</td>
                <td>{{ $order->type->label() }}</td>
                <td class="text-right money">${{ number_format($order->subtotal, 0, ',', '.') }}</td>
                <td class="text-right money">${{ number_format($order->discount, 0, ',', '.') }}</td>
                <td class="text-right money">${{ number_format($order->tax, 0, ',', '.') }}</td>
                <td class="text-right money"><strong>${{ number_format($order->total, 0, ',', '.') }}</strong></td>
                <td>{{ $order->payments->pluck('paymentMethod.name')->implode(', ') ?: 'Pendiente' }}</td>
                <td class="text-center">
                    <span class="badge {{ $order->status->value === 'paid' ? 'badge-success' : 'badge-warning' }}">
                        {{ $order->status->label() }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generado el {{ $generatedAt->format('d/m/Y H:i:s') }} | Sistema POS Restaurante</p>
    </div>
</body>
</html>
