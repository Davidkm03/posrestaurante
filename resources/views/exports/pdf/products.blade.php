<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Productos</title>
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
            border-bottom: 2px solid #059669;
        }
        
        .header h1 {
            font-size: 18px;
            color: #059669;
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
            background: #ecfdf5;
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
            background: #059669;
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
        
        .badge-danger {
            background: #fef2f2;
            color: #991b1b;
        }
        
        .money {
            font-family: 'DejaVu Sans Mono', monospace;
        }
        
        .top-seller {
            background: #fef3c7 !important;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📦 REPORTE DE PRODUCTOS</h1>
        <p>Período: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="value">{{ number_format($summary['total_products']) }}</div>
            <div class="label">Total Productos</div>
        </div>
        <div class="summary-item">
            <div class="value">{{ number_format($summary['products_sold']) }}</div>
            <div class="label">Productos Vendidos</div>
        </div>
        <div class="summary-item">
            <div class="value">{{ number_format($summary['total_quantity']) }}</div>
            <div class="label">Unidades Vendidas</div>
        </div>
        <div class="summary-item">
            <div class="value money">${{ number_format($summary['total_revenue'], 0, ',', '.') }}</div>
            <div class="label">Ingresos Totales</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">Código</th>
                <th style="width: 25%;">Producto</th>
                <th style="width: 12%;">Categoría</th>
                <th class="text-right" style="width: 10%;">Precio</th>
                <th class="text-right" style="width: 10%;">Costo</th>
                <th class="text-center" style="width: 8%;">Cant. Vendida</th>
                <th class="text-right" style="width: 12%;">Total Ventas</th>
                <th class="text-center" style="width: 8%;">Margen</th>
                <th class="text-center" style="width: 7%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $index => $product)
            @php
                $margin = $product->total_sales > 0 
                    ? (($product->total_sales - ($product->cost * $product->total_quantity)) / $product->total_sales) * 100 
                    : 0;
            @endphp
            <tr class="{{ $index < 5 && $product->total_sales > 0 ? 'top-seller' : '' }}">
                <td>{{ $product->sku ?? 'N/A' }}</td>
                <td>
                    <strong>{{ $product->name }}</strong>
                    @if($index < 5 && $product->total_sales > 0)
                        <span style="color: #92400e;">⭐</span>
                    @endif
                </td>
                <td>{{ $product->category->name ?? 'Sin categoría' }}</td>
                <td class="text-right money">${{ number_format($product->price, 0, ',', '.') }}</td>
                <td class="text-right money">${{ number_format($product->cost ?? 0, 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($product->total_quantity) }}</td>
                <td class="text-right money"><strong>${{ number_format($product->total_sales, 0, ',', '.') }}</strong></td>
                <td class="text-center">{{ round($margin, 1) }}%</td>
                <td class="text-center">
                    <span class="badge {{ $product->is_active ? 'badge-success' : 'badge-danger' }}">
                        {{ $product->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>⭐ = Top 5 Productos más vendidos | Generado el {{ $generatedAt->format('d/m/Y H:i:s') }} | Sistema POS Restaurante</p>
    </div>
</body>
</html>
