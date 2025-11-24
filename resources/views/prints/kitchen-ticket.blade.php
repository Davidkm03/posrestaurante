<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comanda - {{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            width: 80mm;
            padding: 3mm;
            background: white;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .large { font-size: 18px; }
        .xlarge { font-size: 24px; }
        .mb { margin-bottom: 3mm; }
        .line { border-top: 2px dashed #000; margin: 3mm 0; }
        .double-line { border-top: 3px solid #000; margin: 3mm 0; }
        .item { margin-bottom: 4mm; padding-bottom: 2mm; border-bottom: 1px dotted #ccc; }
        .item:last-child { border-bottom: none; }
        .item-header { display: flex; align-items: baseline; gap: 3mm; }
        .item-qty { font-size: 20px; font-weight: bold; min-width: 10mm; }
        .item-name { font-size: 16px; font-weight: bold; flex: 1; }
        .modifier { padding-left: 12mm; font-size: 12px; color: #333; }
        .note { padding-left: 12mm; font-size: 12px; font-style: italic; background: #fffacd; padding: 2mm; margin-top: 1mm; }
        .urgent { background: #ffcccc; padding: 2mm; font-weight: bold; }
        .time-box {
            border: 2px solid #000;
            padding: 2mm;
            margin: 2mm 0;
            font-size: 16px;
            font-weight: bold;
        }
        @media print {
            body { width: 80mm; }
            @page { margin: 0; size: 80mm auto; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="center mb">
        <div class="xlarge bold">COCINA</div>
        <div class="line"></div>
    </div>

    <!-- Order Info -->
    <div class="time-box center">
        <div>{{ $order->created_at->format('H:i') }}</div>
    </div>

    <table width="100%" class="mb">
        <tr>
            <td class="large bold">
                @if($order->table)
                    MESA {{ $order->table->number }}
                @else
                    {{ strtoupper($order->type) }}
                @endif
            </td>
            <td style="text-align: right;">
                <div class="bold">{{ $order->order_number }}</div>
            </td>
        </tr>
    </table>

    @if($order->type !== 'dine_in')
        <div class="urgent center mb">
            {{ strtoupper($order->type === 'takeaway' ? 'PARA LLEVAR' : 'DOMICILIO') }}
        </div>
    @endif

    @if($order->guests > 1)
        <div class="center mb">Comensales: {{ $order->guests }}</div>
    @endif

    <div class="double-line"></div>

    <!-- Items grouped by category -->
    @php
        $itemsByCategory = $order->items->groupBy(fn($item) => $item->product->category->name ?? 'Otros');
    @endphp

    @foreach($itemsByCategory as $category => $items)
        <div class="mb">
            <div class="bold" style="background: #eee; padding: 1mm 2mm; margin-bottom: 2mm;">
                {{ strtoupper($category) }}
            </div>

            @foreach($items as $item)
                <div class="item">
                    <div class="item-header">
                        <span class="item-qty">{{ $item->quantity }}x</span>
                        <span class="item-name">{{ $item->product_name }}</span>
                    </div>

                    @if($item->modifiers->isNotEmpty())
                        @foreach($item->modifiers as $mod)
                            <div class="modifier">+ {{ $mod->name }}</div>
                        @endforeach
                    @endif

                    @if($item->notes)
                        <div class="note">*** {{ $item->notes }} ***</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="double-line"></div>

    <!-- Footer -->
    @if($order->notes)
        <div class="urgent mb">
            NOTA: {{ $order->notes }}
        </div>
    @endif

    <div class="center" style="font-size: 10px;">
        <div>Mesero: {{ $order->waiter->name ?? '-' }}</div>
        <div>Impreso: {{ now()->format('H:i:s') }}</div>
    </div>

    <div style="height: 10mm;"></div>

    @if(isset($printAutomatically) && $printAutomatically)
    <script>window.onload = function() { window.print(); }</script>
    @endif
</body>
</html>
