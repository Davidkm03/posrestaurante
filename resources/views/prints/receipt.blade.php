<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recibo - {{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 80mm;
            padding: 5mm;
            background: white;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .mb { margin-bottom: 3mm; }
        .line { border-top: 1px dashed #000; margin: 3mm 0; }
        .double-line { border-top: 2px solid #000; margin: 3mm 0; }
        table { width: 100%; }
        td { vertical-align: top; }
        .logo { max-width: 50mm; margin: 0 auto 3mm; display: block; }
        .item { margin-bottom: 2mm; }
        .item-name { }
        .item-qty { width: 20%; }
        .item-price { width: 30%; text-align: right; }
        .total-row td { padding: 1mm 0; }
        .grand-total { font-size: 14px; font-weight: bold; }
        .qr { width: 30mm; height: 30mm; margin: 3mm auto; }
        @media print {
            body { width: 80mm; }
            @page { margin: 0; size: 80mm auto; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="center mb">
        <div class="bold" style="font-size: 14px;">{{ $order->branch->name ?? config('app.name') }}</div>
        <div>NIT: {{ $order->branch->nit ?? '---' }}</div>
        <div>{{ $order->branch->address ?? '' }}</div>
        <div>Tel: {{ $order->branch->phone ?? '' }}</div>
    </div>

    <div class="line"></div>

    <!-- Order Info -->
    <div class="mb">
        <table>
            <tr>
                <td>Orden:</td>
                <td class="right bold">{{ $order->order_number }}</td>
            </tr>
            <tr>
                <td>Fecha:</td>
                <td class="right">{{ $order->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @if($order->table)
            <tr>
                <td>Mesa:</td>
                <td class="right">{{ $order->table->number }}</td>
            </tr>
            @endif
            @if($order->waiter)
            <tr>
                <td>Atendió:</td>
                <td class="right">{{ $order->waiter->name }}</td>
            </tr>
            @endif
        </table>
    </div>

    @if($order->customer)
    <div class="line"></div>
    <div class="mb">
        <div class="bold">Cliente:</div>
        <div>{{ $order->customer->name }}</div>
        <div>{{ $order->customer->document_type }}: {{ $order->customer->document_number }}</div>
    </div>
    @endif

    <div class="double-line"></div>

    <!-- Items -->
    <div class="mb">
        @foreach($order->items as $item)
        <div class="item">
            <table>
                <tr>
                    <td class="item-qty">{{ $item->quantity }}x</td>
                    <td class="item-name">{{ $item->product_name }}</td>
                    <td class="item-price">${{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            </table>
            @if($item->modifiers->isNotEmpty())
                @foreach($item->modifiers as $mod)
                <div style="padding-left: 8mm; font-size: 10px; color: #666;">
                    + {{ $mod->name }}
                </div>
                @endforeach
            @endif
            @if($item->notes)
                <div style="padding-left: 8mm; font-size: 10px; font-style: italic;">
                    Nota: {{ $item->notes }}
                </div>
            @endif
        </div>
        @endforeach
    </div>

    <div class="double-line"></div>

    <!-- Totals -->
    <table class="mb">
        <tr class="total-row">
            <td>Subtotal:</td>
            <td class="right">${{ number_format($order->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($order->discount > 0)
        <tr class="total-row">
            <td>Descuento:</td>
            <td class="right">-${{ number_format($order->discount, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="total-row">
            <td>IVA:</td>
            <td class="right">${{ number_format($order->tax, 0, ',', '.') }}</td>
        </tr>
        @if($order->tip > 0)
        <tr class="total-row">
            <td>Propina:</td>
            <td class="right">${{ number_format($order->tip, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="total-row grand-total">
            <td>TOTAL:</td>
            <td class="right">${{ number_format($order->total + ($order->tip ?? 0), 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- Payments -->
    @if($order->payments->isNotEmpty())
    <div class="line"></div>
    <div class="mb">
        <div class="bold mb">Forma de Pago:</div>
        @foreach($order->payments as $payment)
        <table>
            <tr>
                <td>{{ $payment->method->name ?? 'Efectivo' }}</td>
                <td class="right">${{ number_format($payment->amount, 0, ',', '.') }}</td>
            </tr>
        </table>
        @endforeach
        @if($order->change_amount > 0)
        <table>
            <tr>
                <td>Cambio:</td>
                <td class="right">${{ number_format($order->change_amount, 0, ',', '.') }}</td>
            </tr>
        </table>
        @endif
    </div>
    @endif

    <div class="line"></div>

    <!-- Footer -->
    <div class="center" style="font-size: 10px; margin-top: 3mm;">
        <p>Gracias por su visita</p>
        <p>{{ config('app.name') }}</p>
        <p style="margin-top: 2mm;">{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    @if(isset($printAutomatically) && $printAutomatically)
    <script>window.onload = function() { window.print(); }</script>
    @endif
</body>
</html>
