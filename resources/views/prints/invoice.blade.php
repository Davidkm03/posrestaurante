<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factura {{ $invoice->full_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            padding: 10mm;
            max-width: 210mm;
        }
        .header { display: flex; justify-content: space-between; margin-bottom: 8mm; }
        .logo-section { width: 40%; }
        .invoice-section { width: 55%; text-align: right; }
        .invoice-title { font-size: 20px; font-weight: bold; color: #1a56db; margin-bottom: 3mm; }
        .invoice-number { font-size: 14px; font-weight: bold; }
        .company-info { margin-bottom: 5mm; }
        .company-name { font-size: 16px; font-weight: bold; margin-bottom: 2mm; }
        table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 2mm 3mm; vertical-align: top; }
        .info-table .label { font-weight: bold; width: 30%; background: #f3f4f6; }
        .items-table { margin: 5mm 0; }
        .items-table th { background: #1a56db; color: white; padding: 3mm; text-align: left; }
        .items-table td { padding: 3mm; border-bottom: 1px solid #e5e7eb; }
        .items-table .qty { width: 10%; text-align: center; }
        .items-table .price { width: 15%; text-align: right; }
        .items-table .tax { width: 10%; text-align: center; }
        .items-table .subtotal { width: 15%; text-align: right; }
        .totals { margin-top: 5mm; }
        .totals-table { width: 50%; margin-left: auto; }
        .totals-table td { padding: 2mm 3mm; }
        .totals-table .label { text-align: right; }
        .totals-table .value { text-align: right; font-weight: bold; }
        .totals-table .grand-total { font-size: 14px; background: #f3f4f6; }
        .footer { margin-top: 10mm; padding-top: 5mm; border-top: 1px solid #e5e7eb; }
        .legal-text { font-size: 9px; color: #6b7280; margin-top: 5mm; }
        .qr-section { display: flex; justify-content: space-between; align-items: flex-start; margin-top: 5mm; }
        .qr-code { width: 25mm; height: 25mm; border: 1px solid #ddd; }
        .cufe { font-family: monospace; font-size: 8px; word-break: break-all; max-width: 70%; }
        @media print {
            body { padding: 5mm; }
            @page { margin: 10mm; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo-section">
            <div class="company-info">
                <div class="company-name">{{ $invoice->branch->name ?? config('app.name') }}</div>
                <div>NIT: {{ $invoice->branch->nit ?? '---' }}</div>
                <div>{{ $invoice->branch->address ?? '' }}</div>
                <div>{{ $invoice->branch->city ?? '' }}, {{ $invoice->branch->department ?? '' }}</div>
                <div>Tel: {{ $invoice->branch->phone ?? '' }}</div>
                <div>Email: {{ $invoice->branch->email ?? '' }}</div>
            </div>
        </div>
        <div class="invoice-section">
            <div class="invoice-title">FACTURA ELECTRÓNICA DE VENTA</div>
            <div class="invoice-number">{{ $invoice->full_number }}</div>
            <div style="margin-top: 3mm;">
                <strong>Fecha:</strong> {{ $invoice->issue_date->format('d/m/Y') }}<br>
                <strong>Hora:</strong> {{ $invoice->issue_date->format('H:i:s') }}<br>
                <strong>Vencimiento:</strong> {{ $invoice->due_date?->format('d/m/Y') ?? '-' }}
            </div>
        </div>
    </div>

    <!-- Resolution Info -->
    <div style="background: #f9fafb; padding: 3mm; margin-bottom: 5mm; font-size: 9px;">
        @if($invoice->resolution)
            Resolución DIAN No. {{ $invoice->resolution->resolution_number }} del {{ $invoice->resolution->resolution_date?->format('d/m/Y') }}.
            Autoriza del {{ $invoice->resolution->prefix }}{{ $invoice->resolution->range_from }} al {{ $invoice->resolution->prefix }}{{ $invoice->resolution->range_to }}.
            Vigencia hasta {{ $invoice->resolution->valid_until?->format('d/m/Y') }}.
        @endif
    </div>

    <!-- Customer Info -->
    <table class="info-table" style="margin-bottom: 5mm;">
        <tr>
            <td class="label">Cliente:</td>
            <td>{{ $invoice->customer_name }}</td>
            <td class="label">{{ $invoice->customer_document_type }}:</td>
            <td>{{ $invoice->customer_document_number }}</td>
        </tr>
        <tr>
            <td class="label">Dirección:</td>
            <td>{{ $invoice->customer_address ?? '-' }}</td>
            <td class="label">Ciudad:</td>
            <td>{{ $invoice->customer_city ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Email:</td>
            <td colspan="3">{{ $invoice->customer_email ?? '-' }}</td>
        </tr>
    </table>

    <!-- Items -->
    <table class="items-table">
        <thead>
            <tr>
                <th>Descripción</th>
                <th class="qty">Cant.</th>
                <th class="price">Precio Unit.</th>
                <th class="tax">IVA</th>
                <th class="subtotal">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="qty">{{ $item->quantity }}</td>
                <td class="price">${{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="tax">{{ $item->tax_percentage }}%</td>
                <td class="subtotal">${{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals">
        <table class="totals-table">
            <tr>
                <td class="label">Subtotal:</td>
                <td class="value">${{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($invoice->discount > 0)
            <tr>
                <td class="label">Descuento:</td>
                <td class="value">-${{ number_format($invoice->discount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Base gravable:</td>
                <td class="value">${{ number_format($invoice->tax_base, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">IVA:</td>
                <td class="value">${{ number_format($invoice->tax_amount, 0, ',', '.') }}</td>
            </tr>
            <tr class="grand-total">
                <td class="label">TOTAL:</td>
                <td class="value">${{ number_format($invoice->total, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <!-- QR and CUFE -->
    <div class="qr-section">
        <div class="cufe">
            <strong>CUFE:</strong><br>
            {{ $invoice->cufe ?? 'Pendiente de generación' }}
        </div>
        <div class="qr-code">
            <!-- QR Code placeholder -->
            @if($invoice->cufe)
                <img src="data:image/svg+xml,{{ urlencode('<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\'><rect fill=\'#eee\' width=\'100\' height=\'100\'/><text x=\'50\' y=\'50\' text-anchor=\'middle\' dy=\'.3em\' font-size=\'10\'>QR</text></svg>') }}" alt="QR">
            @endif
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="legal-text">
            <p>Esta factura electrónica se expide según lo establecido en el Decreto 2242 de 2015 y la Resolución DIAN 000042 de 2020.</p>
            <p>Documento generado por software autorizado.</p>
            @if($invoice->notes)
                <p><strong>Observaciones:</strong> {{ $invoice->notes }}</p>
            @endif
        </div>
    </div>

    @if(isset($printAutomatically) && $printAutomatically)
    <script>window.onload = function() { window.print(); }</script>
    @endif
</body>
</html>
