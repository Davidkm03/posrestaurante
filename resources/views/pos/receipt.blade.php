<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo #{{ $order->id }}</title>
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

        .payments {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
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
            <span>Recibo:</span>
            <span><strong>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong></span>
        </div>
        <div class="info-row">
            <span>Fecha:</span>
            <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
        </div>
        @if($order->table)
        <div class="info-row">
            <span>Mesa:</span>
            <span>{{ $order->table->name }}</span>
        </div>
        @endif
        @if($order->waiter)
        <div class="info-row">
            <span>Mesero:</span>
            <span>{{ $order->waiter->name }}</span>
        </div>
        @endif
        @if($order->customer)
        <div class="info-row">
            <span>Cliente:</span>
            <span>{{ $order->customer->name }}</span>
        </div>
        @endif
    </div>

    <div class="items">
        @foreach($order->items as $item)
        <div class="item">
            <div class="item-name">{{ $item->name }}</div>
            <div class="item-details">
                <span>{{ intval($item->quantity) }}x ${{ number_format($item->price, 0, ',', '.') }}</span>
                <span><strong>${{ number_format($item->total, 0, ',', '.') }}</strong></span>
            </div>
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

    <div class="payments">
        <p style="font-weight: bold; margin-bottom: 5px;">PAGOS:</p>
        @foreach($payments as $payment)
        <div class="payment-row">
            <span>{{ ucfirst($payment['method']) }}:</span>
            <span>${{ number_format($payment['amount'], 0, ',', '.') }}</span>
        </div>
        @if(!empty($payment['reference']))
        <div class="payment-row" style="font-size: 10px;">
            <span>Ref:</span>
            <span>{{ $payment['reference'] }}</span>
        </div>
        @endif
        @endforeach
        <div class="payment-row" style="margin-top: 5px; padding-top: 5px; border-top: 1px solid #000;">
            <span><strong>Total Pagado:</strong></span>
            <span><strong>${{ number_format($totalPaid, 0, ',', '.') }}</strong></span>
        </div>
        @if($change > 0)
        <div class="payment-row" style="font-size: 14px; font-weight: bold;">
            <span>CAMBIO:</span>
            <span>${{ number_format($change, 0, ',', '.') }}</span>
        </div>
        @endif
    </div>

    <div class="footer">
        <p>¡Gracias por su compra!</p>
        <p>Vuelva pronto</p>
        <p style="margin-top: 10px; font-size: 10px;">{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <button class="print-button no-print" onclick="window.print()">
        🖨️ Imprimir
    </button>

    <script>
        // Auto print on load (optional)
        // window.addEventListener('load', function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 500);
        // });
    </script>
</body>
</html>
