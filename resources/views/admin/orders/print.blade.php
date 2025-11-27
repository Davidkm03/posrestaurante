<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden #{{ $order->order_number }}</title>
    <style>
        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            padding: 10px;
            max-width: 80mm;
            margin: 0 auto;
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            margin: 2px 0;
        }

        .info {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }

        .items {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
        }

        .item {
            margin-bottom: 8px;
        }

        .item-name {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }

        .modifier {
            font-size: 10px;
            margin-left: 10px;
            color: #333;
        }

        .totals {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }

        .total-row.final {
            font-weight: bold;
            font-size: 14px;
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px solid #000;
        }

        .footer {
            text-align: center;
            font-size: 11px;
            margin-top: 10px;
        }

        .footer p {
            margin: 2px 0;
        }

        .print-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #3b82f6;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .print-button:hover {
            background: #2563eb;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .status-pending { background: #fef3c7; color: #92400e; }
        .status-in_preparation { background: #dbeafe; color: #1e40af; }
        .status-ready { background: #d1fae5; color: #065f46; }
        .status-paid { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $order->branch->name }}</h1>
        <p>{{ $order->branch->address ?? 'Dirección no disponible' }}</p>
        <p>NIT: {{ $order->branch->nit ?? 'N/A' }}</p>
        <p>Tel: {{ $order->branch->phone ?? 'N/A' }}</p>
    </div>

    <div class="info">
        <div class="info-row">
            <span>Orden:</span>
            <span><strong>#{{ $order->order_number }}</strong></span>
        </div>
        <div class="info-row">
            <span>Fecha:</span>
            <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span>Estado:</span>
            <span class="status-badge status-{{ $order->status->value }}">
                {{ $order->status->label() }}
            </span>
        </div>
        @if($order->table)
        <div class="info-row">
            <span>Mesa:</span>
            <span>{{ $order->table->name }}</span>
        </div>
        @endif
        @if($order->user)
        <div class="info-row">
            <span>Mesero:</span>
            <span>{{ $order->user->name }}</span>
        </div>
        @endif
        @if($order->customer)
        <div class="info-row">
            <span>Cliente:</span>
            <span>{{ $order->customer->name }}</span>
        </div>
        @endif
        <div class="info-row">
            <span>Tipo:</span>
            <span>{{ $order->type->label() }}</span>
        </div>
    </div>

    <div class="items">
        <h3 style="margin-bottom: 5px;">ITEMS:</h3>
        @foreach($order->items as $item)
        <div class="item">
            <div class="item-name">{{ $item->name }}</div>
            <div class="item-details">
                <span>{{ intval($item->quantity) }}x ${{ number_format($item->price, 0, ',', '.') }}</span>
                <span><strong>${{ number_format($item->total, 0, ',', '.') }}</strong></span>
            </div>
            @if($item->notes)
            <div class="modifier">
                📝 {{ $item->notes }}
            </div>
            @endif
            @foreach($item->modifiers as $modifier)
            <div class="modifier">
                + {{ $modifier->name }} (${{ number_format($modifier->price, 0, ',', '.') }})
            </div>
            @endforeach
        </div>
        @endforeach
    </div>

    <div class="totals">
        @if($order->discount_amount > 0)
        <div class="total-row">
            <span>Subtotal:</span>
            <span>${{ number_format($order->subtotal + $order->discount_amount, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>Descuento:</span>
            <span>-${{ number_format($order->discount_amount, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="total-row final">
            <span>TOTAL:</span>
            <span>${{ number_format($order->total, 0, ',', '.') }}</span>
        </div>
        <div style="text-align: right; font-size: 10px; margin-top: 5px;">
            * Precio incluye IVA
        </div>
    </div>

    @if($order->payment_status->value === 'completed')
    <div style="border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px;">
        <div class="total-row">
            <span><strong>Estado de Pago:</strong></span>
            <span><strong>PAGADO</strong></span>
        </div>
        <div class="total-row">
            <span>Monto Pagado:</span>
            <span>${{ number_format($order->paid_amount, 0, ',', '.') }}</span>
        </div>
    </div>
    @endif

    @if($order->notes)
    <div style="border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px;">
        <p style="font-weight: bold; margin-bottom: 3px;">Notas:</p>
        <p style="font-size: 11px;">{{ $order->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Impreso: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p style="margin-top: 10px;">¡Gracias por su preferencia!</p>
    </div>

    <button class="print-button no-print" onclick="window.print()">
        🖨️ Imprimir
    </button>

    <script>
        // Auto print on load (optional - uncomment if needed)
        // window.addEventListener('load', function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 500);
        // });
    </script>
</body>
</html>
