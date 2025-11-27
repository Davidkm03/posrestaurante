<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cierre de Caja</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #1f2937;
            padding: 20px;
        }
        
        .document {
            max-width: 600px;
            margin: 0 auto;
            border: 2px solid #1f2937;
            padding: 30px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px double #1f2937;
        }
        
        .header h1 {
            font-size: 20px;
            color: #1f2937;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .header p {
            font-size: 10px;
            color: #6b7280;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-section h2 {
            font-size: 12px;
            color: #374151;
            text-transform: uppercase;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        
        .info-label {
            display: table-cell;
            width: 40%;
            font-weight: bold;
            color: #374151;
        }
        
        .info-value {
            display: table-cell;
            width: 60%;
            text-align: right;
        }
        
        .totals-section {
            background: #f3f4f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        
        .total-row:last-child {
            margin-bottom: 0;
        }
        
        .total-label {
            display: table-cell;
            width: 50%;
            font-size: 11px;
        }
        
        .total-value {
            display: table-cell;
            width: 50%;
            text-align: right;
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 11px;
        }
        
        .grand-total {
            font-size: 14px;
            font-weight: bold;
            padding-top: 10px;
            border-top: 2px solid #374151;
            margin-top: 10px;
        }
        
        .difference {
            margin-top: 15px;
            padding: 10px;
            border-radius: 4px;
        }
        
        .difference.positive {
            background: #dcfce7;
            border: 1px solid #86efac;
        }
        
        .difference.negative {
            background: #fef2f2;
            border: 1px solid #fecaca;
        }
        
        .difference.zero {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
        }
        
        .signature-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        
        .signature-row {
            display: table;
            width: 100%;
        }
        
        .signature-box {
            display: table-cell;
            width: 45%;
            text-align: center;
            padding: 20px 10px 0;
        }
        
        .signature-line {
            border-top: 1px solid #1f2937;
            padding-top: 5px;
            font-size: 10px;
            color: #6b7280;
        }
        
        .notes-section {
            margin-top: 20px;
            padding: 10px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 4px;
        }
        
        .notes-section h3 {
            font-size: 10px;
            color: #92400e;
            margin-bottom: 5px;
        }
        
        .notes-section p {
            font-size: 10px;
            color: #78350f;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="document">
        <div class="header">
            <h1>🧾 Cierre de Caja</h1>
            <p>Documento de Control Interno</p>
        </div>

        <div class="info-section">
            <h2>Información General</h2>
            <div class="info-row">
                <div class="info-label">Caja:</div>
                <div class="info-value">{{ $session->cashRegister->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Cajero:</div>
                <div class="info-value">{{ $session->user->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha Apertura:</div>
                <div class="info-value">{{ $session->opened_at?->format('d/m/Y H:i') ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha Cierre:</div>
                <div class="info-value">{{ $session->closed_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Duración:</div>
                <div class="info-value">
                    @if($session->opened_at && $session->closed_at)
                        {{ $session->opened_at->diffInHours($session->closed_at) }} horas {{ $session->opened_at->diff($session->closed_at)->format('%I') }} minutos
                    @else
                        N/A
                    @endif
                </div>
            </div>
        </div>

        <div class="totals-section">
            <div class="total-row">
                <div class="total-label">Monto de Apertura:</div>
                <div class="total-value">${{ number_format($session->opening_amount ?? 0, 0, ',', '.') }}</div>
            </div>

            <div class="total-row" style="margin-top: 15px;">
                <div class="total-label"><strong>VENTAS EN EFECTIVO:</strong></div>
                <div class="total-value"><strong>${{ number_format($session->cash_sales ?? 0, 0, ',', '.') }}</strong></div>
            </div>
            <div class="total-row">
                <div class="total-label">Ventas con Tarjeta:</div>
                <div class="total-value">${{ number_format($session->card_sales ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="total-row">
                <div class="total-label">Otros Métodos:</div>
                <div class="total-value">${{ number_format($session->other_sales ?? 0, 0, ',', '.') }}</div>
            </div>

            <div class="total-row grand-total">
                <div class="total-label">TOTAL VENTAS:</div>
                <div class="total-value">${{ number_format($session->total_sales ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="info-section">
            <h2>Arqueo de Caja</h2>
            <div class="info-row">
                <div class="info-label">Efectivo Esperado:</div>
                <div class="info-value">${{ number_format(($session->opening_amount ?? 0) + ($session->cash_sales ?? 0), 0, ',', '.') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Efectivo Contado:</div>
                <div class="info-value">${{ number_format($session->closing_amount ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>

        @php
            $expected = ($session->opening_amount ?? 0) + ($session->cash_sales ?? 0);
            $counted = $session->closing_amount ?? 0;
            $difference = $counted - $expected;
        @endphp

        <div class="difference {{ $difference > 0 ? 'positive' : ($difference < 0 ? 'negative' : 'zero') }}">
            <div class="total-row">
                <div class="total-label">
                    <strong>
                        @if($difference > 0)
                            ✓ SOBRANTE:
                        @elseif($difference < 0)
                            ✗ FALTANTE:
                        @else
                            ✓ CUADRE EXACTO
                        @endif
                    </strong>
                </div>
                <div class="total-value">
                    <strong>
                        @if($difference != 0)
                            ${{ number_format(abs($difference), 0, ',', '.') }}
                        @endif
                    </strong>
                </div>
            </div>
        </div>

        @if($session->notes)
        <div class="notes-section">
            <h3>📝 Observaciones</h3>
            <p>{{ $session->notes }}</p>
        </div>
        @endif

        <div class="signature-section">
            <div class="signature-row">
                <div class="signature-box">
                    <div class="signature-line">Firma del Cajero</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">Firma del Supervisor</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Documento generado el {{ $generatedAt->format('d/m/Y H:i:s') }}</p>
            <p>Sistema POS Restaurante - Cierre de Caja #{{ $session->id }}</p>
        </div>
    </div>
</body>
</html>
