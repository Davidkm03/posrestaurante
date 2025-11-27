<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Customer;
use App\Enums\InvoiceStatus;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['customer', 'order', 'order.user'])
            ->latest();

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('invoice_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('invoice_date', '<=', $request->to_date);
        }

        $invoices = $query->paginate(20);

        $statuses = InvoiceStatus::cases();

        return view('admin.invoices.index', compact('invoices', 'statuses'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'order.items.product', 'order.user', 'payments', 'lines', 'resolution', 'branch']);

        return view('admin.invoices.show', compact('invoice'));
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['customer', 'order.items.product', 'order.user', 'payments', 'lines', 'resolution', 'branch']);

        return view('admin.invoices.print', compact('invoice'));
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['customer', 'order.items.product', 'order.user', 'payments']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.invoices.pdf', compact('invoice'));
        
        return $pdf->download("factura-{$invoice->invoice_number}.pdf");
    }

    public function cancel(Invoice $invoice)
    {
        if ($invoice->status === InvoiceStatus::VOIDED) {
            return back()->with('error', 'La factura ya está anulada.');
        }

        $invoice->update([
            'status' => InvoiceStatus::VOIDED,
            'voided_at' => now(),
        ]);

        return back()->with('success', 'Factura anulada exitosamente.');
    }
}
