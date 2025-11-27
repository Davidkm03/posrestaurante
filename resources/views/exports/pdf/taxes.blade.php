<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Impuestos</title>
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
            border-bottom: 2px solid #dc2626;
        }
        
        .header h1 {
            font-size: 18px;
            color: #dc2626;
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
            background: #fef2f2;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #fecaca;
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
        
        .summary-item.highlight .value {
            color: #dc2626;
            font-size: 16px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th {
            background: #dc2626;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
        }
        
        td {
            padding: 8px 5px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
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
        
        tfoot {
            font-weight: bold;
            background: #f3f4f6;
        }
        
        tfoot td {
            border-top: 2px solid #dc2626;
            padding-top: 10px;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 8px;
            color: #9ca3af;
            text-align: center;
        }
        
        .money {
            font-family: 'DejaVu Sans Mono', monospace;
        }
        
        .tax-info {
            margin-top: 20px;
            padding: 15px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 4px;
        }
        
        .tax-info h3 {
            font-size: 11px;
            color: #92400e;
            margin-bottom: 10px;
        }
        
        .tax-info p {
            font-size: 9px;
            color: #78350f;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📋 REPORTE DE IMPUESTOS</h1>
        <p>Período: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="value">{{ number_format($summary['total_days']) }}</div>
            <div class="label">Días Reportados</div>
        </div>
        <div class="summary-item">
            <div class="value">{{ number_format($summary['total_orders']) }}</div>
            <div class="label">Total Órdenes</div>
        </div>
        <div class="summary-item">
            <div class="value money">${{ number_format($summary['total_subtotal'], 0, ',', '.') }}</div>
            <div class="label">Base Imponible</div>
        </div>
        <div class="summary-item highlight">
            <div class="value money">${{ number_format($summary['total_tax'], 0, ',', '.') }}</div>
            <div class="label">IVA Recaudado</div>
        </div>
        <div class="summary-item">
            <div class="value money">${{ number_format($summary['total_sales'], 0, ',', '.') }}</div>
            <div class="label">Total Ventas</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 12%;">Fecha</th>
                <th class="text-center" style="width: 10%;">Nº Órdenes</th>
                <th class="text-right" style="width: 15%;">Subtotal</th>
                <th class="text-right" style="width: 13%;">Descuentos</th>
                <th class="text-right" style="width: 15%;">Base Gravable</th>
                <th class="text-right" style="width: 15%;">IVA (19%)</th>
                <th class="text-right" style="width: 15%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            @php
                $baseGravable = $row->total_subtotal - $row->total_discounts;
            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}</td>
                <td class="text-center">{{ number_format($row->total_orders) }}</td>
                <td class="text-right money">${{ number_format($row->total_subtotal, 0, ',', '.') }}</td>
                <td class="text-right money">${{ number_format($row->total_discounts, 0, ',', '.') }}</td>
                <td class="text-right money">${{ number_format($baseGravable, 0, ',', '.') }}</td>
                <td class="text-right money" style="color: #dc2626;">${{ number_format($row->total_tax, 0, ',', '.') }}</td>
                <td class="text-right money"><strong>${{ number_format($row->total_sales, 0, ',', '.') }}</strong></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>TOTALES</td>
                <td class="text-center">{{ number_format($summary['total_orders']) }}</td>
                <td class="text-right money">${{ number_format($summary['total_subtotal'], 0, ',', '.') }}</td>
                <td class="text-right money">${{ number_format($summary['total_discounts'], 0, ',', '.') }}</td>
                <td class="text-right money">${{ number_format($summary['total_subtotal'] - $summary['total_discounts'], 0, ',', '.') }}</td>
                <td class="text-right money" style="color: #dc2626;">${{ number_format($summary['total_tax'], 0, ',', '.') }}</td>
                <td class="text-right money">${{ number_format($summary['total_sales'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="tax-info">
        <h3>ℹ️ Información Fiscal</h3>
        <p><strong>IVA General:</strong> 19% sobre base gravable</p>
        <p><strong>Base Gravable:</strong> Subtotal - Descuentos</p>
        <p><strong>Período Fiscal:</strong> {{ $startDate->format('F Y') }}</p>
        <p><strong>Responsable:</strong> Este reporte es para uso interno y control tributario.</p>
    </div>

    <div class="footer">
        <p>Generado el {{ $generatedAt->format('d/m/Y H:i:s') }} | Sistema POS Restaurante | Documento para control interno</p>
    </div>
</body>
</html>
