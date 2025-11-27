<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comanda #{{ $order->order_number }}</title>
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
            font-size: 14px;
            line-height: 1.4;
            padding: 10px;
            max-width: 80mm;
            margin: 0 auto;
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header .order-number {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0;
        }

        .info {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-size: 14px;
        }

        .info-row strong {
            font-size: 16px;
        }

        .items {
            margin-bottom: 15px;
        }

        .item {
            margin-bottom: 15px;
            padding: 10px;
            background: #f5f5f5;
            border: 2px solid #000;
            border-radius: 5px;
        }

        .item-quantity {
            font-size: 32px;
            font-weight: bold;
            float: left;
            margin-right: 10px;
            line-height: 1;
        }

        .item-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .item-notes {
            font-size: 14px;
            margin: 8px 0;
            padding: 5px;
            background: #fff3cd;
            border-left: 3px solid #ffc107;
        }

        .modifier {
            font-size: 13px;
            margin-left: 20px;
            margin-top: 5px;
            padding-left: 10px;
            border-left: 2px solid #666;
        }

        .category {
            display: inline-block;
            padding: 3px 8px;
            background: #e9ecef;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            margin-top: 5px;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #000;
        }

        .footer p {
            margin: 3px 0;
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

        .urgent {
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0%, 50%, 100% { opacity: 1; }
            25%, 75% { opacity: 0.5; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🍳 COCINA 🍳</h1>
        <div class="order-number">#{{ $order->order_number }}</div>
    </div>

    <div class="info">
        <div class="info-row">
            <span>Hora:</span>
            <span><strong>{{ $order->created_at->format('H:i') }}</strong></span>
        </div>
        @if($order->table)
        <div class="info-row">
            <span>Mesa:</span>
            <span><strong>{{ $order->table->name }}</strong></span>
        </div>
        @else
        <div class="info-row">
            <span>Tipo:</span>
            <span><strong>{{ $order->type->label() }}</strong></span>
        </div>
        @endif
        @if($order->user)
        <div class="info-row">
            <span>Mesero:</span>
            <span>{{ $order->user->name }}</span>
        </div>
        @endif
        @if($order->guests > 1)
        <div class="info-row">
            <span>Comensales:</span>
            <span><strong>{{ $order->guests }}</strong></span>
        </div>
        @endif
    </div>

    <div class="items">
        @foreach($order->items as $item)
        <div class="item">
            <div class="item-quantity">{{ intval($item->quantity) }}x</div>
            <div>
                <div class="item-name">{{ $item->name }}</div>
                
                @if($item->product && $item->product->category)
                <span class="category">{{ $item->product->category->name }}</span>
                @endif

                @foreach($item->modifiers as $modifier)
                <div class="modifier">
                    ➤ {{ $modifier->name }}
                    @if($modifier->quantity > 1)
                        ({{ intval($modifier->quantity) }})
                    @endif
                </div>
                @endforeach

                @if($item->notes)
                <div class="item-notes">
                    📝 <strong>NOTA:</strong> {{ $item->notes }}
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    @if($order->kitchen_notes)
    <div style="border: 3px solid #ffc107; padding: 10px; margin-bottom: 15px; background: #fff3cd;">
        <strong style="font-size: 16px;">⚠️ NOTAS ESPECIALES:</strong>
        <p style="font-size: 14px; margin-top: 5px;">{{ $order->kitchen_notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p><strong>Total Items: {{ $order->items->sum('quantity') }}</strong></p>
        <p>Impreso: {{ now()->format('d/m/Y H:i:s') }}</p>
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
