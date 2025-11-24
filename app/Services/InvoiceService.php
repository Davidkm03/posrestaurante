<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Invoice;
use App\Models\DIANResolution;
use App\Models\Customer;
use App\Enums\InvoiceStatus;
use App\Enums\DIANDocumentType;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceService
{
    public function generateFromOrder(Order $order): Invoice
    {
        if (!$order->customer_id) {
            throw new \InvalidArgumentException('La orden debe tener un cliente para facturar');
        }

        return DB::transaction(function () use ($order) {
            $resolution = $this->getActiveResolution($order->branch_id);

            if (!$resolution) {
                throw new \RuntimeException('No hay resolución DIAN activa');
            }

            $invoiceNumber = $this->getNextInvoiceNumber($resolution);
            $customer = $order->customer;

            $invoice = Invoice::create([
                'order_id' => $order->id,
                'branch_id' => $order->branch_id,
                'customer_id' => $customer->id,
                'dian_resolution_id' => $resolution->id,
                'invoice_number' => $invoiceNumber,
                'prefix' => $resolution->prefix,
                'document_type' => DIANDocumentType::INVOICE->value,
                'issue_date' => now(),
                'due_date' => now()->addDays(30),
                'subtotal' => $order->subtotal,
                'discount' => $order->discount,
                'tax_base' => $order->subtotal - $order->discount,
                'tax_amount' => $order->tax,
                'total' => $order->total,
                'status' => InvoiceStatus::DRAFT->value,
                'customer_name' => $customer->name,
                'customer_document_type' => $customer->document_type,
                'customer_document_number' => $customer->document_number,
                'customer_address' => $customer->address,
                'customer_city' => $customer->city,
                'customer_email' => $customer->email,
            ]);

            // Copy items to invoice
            foreach ($order->items as $item) {
                $invoice->items()->create([
                    'product_id' => $item->product_id,
                    'description' => $item->product_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount' => 0,
                    'tax_type' => $item->tax_type,
                    'tax_percentage' => $item->tax_percentage,
                    'tax_amount' => $item->tax_amount,
                    'subtotal' => $item->subtotal,
                ]);
            }

            // Update resolution counter
            $resolution->increment('current_number');

            return $invoice->fresh(['items', 'customer']);
        });
    }

    public function generateCUFE(Invoice $invoice): string
    {
        // CUFE = SHA-384 hash of specific fields
        // This is a simplified version - real implementation needs exact DIAN specs
        $data = implode('', [
            $invoice->invoice_number,
            $invoice->issue_date->format('Y-m-d'),
            $invoice->issue_date->format('H:i:s'),
            number_format($invoice->subtotal, 2, '.', ''),
            '01', // Tax code
            number_format($invoice->tax_amount, 2, '.', ''),
            number_format($invoice->total, 2, '.', ''),
            $invoice->branch->nit ?? '',
            $invoice->customer_document_number,
            config('dian.technical_key', ''),
            config('dian.environment', 'test'),
        ]);

        return hash('sha384', $data);
    }

    public function generateQRCode(Invoice $invoice): string
    {
        $qrData = sprintf(
            "NumFac: %s\nFecFac: %s\nNitFac: %s\nDocAdq: %s\nValFac: %s\nValIva: %s\nValOtroIm: %s\nValTotFac: %s\nCUFE: %s",
            $invoice->full_number,
            $invoice->issue_date->format('Y-m-d'),
            $invoice->branch->nit ?? '',
            $invoice->customer_document_number,
            number_format($invoice->subtotal, 2, '.', ''),
            number_format($invoice->tax_amount, 2, '.', ''),
            '0.00',
            number_format($invoice->total, 2, '.', ''),
            $invoice->cufe ?? ''
        );

        return $qrData;
    }

    public function sendToDIAN(Invoice $invoice): array
    {
        // Generate CUFE if not exists
        if (!$invoice->cufe) {
            $cufe = $this->generateCUFE($invoice);
            $invoice->update(['cufe' => $cufe]);
        }

        // TODO: Implement actual DIAN communication
        // This would involve:
        // 1. Generate UBL 2.1 XML
        // 2. Sign XML with digital certificate
        // 3. Send to DIAN web service
        // 4. Process response

        // For now, simulate success
        $invoice->update([
            'status' => InvoiceStatus::SENT->value,
            'sent_at' => now(),
            'dian_response' => [
                'status' => 'simulated',
                'message' => 'Factura enviada (simulación)',
            ],
        ]);

        return [
            'success' => true,
            'message' => 'Factura enviada a DIAN',
            'cufe' => $invoice->cufe,
        ];
    }

    public function createCreditNote(Invoice $invoice, array $items, string $reason): Invoice
    {
        return DB::transaction(function () use ($invoice, $items, $reason) {
            $resolution = $this->getActiveResolution($invoice->branch_id, 'credit_note');

            $creditNote = Invoice::create([
                'order_id' => $invoice->order_id,
                'branch_id' => $invoice->branch_id,
                'customer_id' => $invoice->customer_id,
                'dian_resolution_id' => $resolution->id,
                'related_invoice_id' => $invoice->id,
                'invoice_number' => $this->getNextInvoiceNumber($resolution),
                'prefix' => $resolution->prefix,
                'document_type' => DIANDocumentType::CREDIT_NOTE->value,
                'issue_date' => now(),
                'subtotal' => 0,
                'discount' => 0,
                'tax_base' => 0,
                'tax_amount' => 0,
                'total' => 0,
                'status' => InvoiceStatus::DRAFT->value,
                'customer_name' => $invoice->customer_name,
                'customer_document_type' => $invoice->customer_document_type,
                'customer_document_number' => $invoice->customer_document_number,
                'notes' => $reason,
            ]);

            $subtotal = 0;
            $taxAmount = 0;

            foreach ($items as $itemData) {
                $originalItem = $invoice->items()->find($itemData['item_id']);
                $quantity = $itemData['quantity'] ?? $originalItem->quantity;

                $itemSubtotal = $originalItem->unit_price * $quantity;
                $itemTax = ($itemSubtotal * $originalItem->tax_percentage) / 100;

                $creditNote->items()->create([
                    'product_id' => $originalItem->product_id,
                    'description' => $originalItem->description,
                    'quantity' => $quantity,
                    'unit_price' => $originalItem->unit_price,
                    'tax_type' => $originalItem->tax_type,
                    'tax_percentage' => $originalItem->tax_percentage,
                    'tax_amount' => $itemTax,
                    'subtotal' => $itemSubtotal,
                ]);

                $subtotal += $itemSubtotal;
                $taxAmount += $itemTax;
            }

            $creditNote->update([
                'subtotal' => $subtotal,
                'tax_base' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => $subtotal + $taxAmount,
            ]);

            $resolution->increment('current_number');

            return $creditNote->fresh(['items']);
        });
    }

    protected function getActiveResolution(int $branchId, string $type = 'invoice'): ?DIANResolution
    {
        return DIANResolution::where('branch_id', $branchId)
            ->where('document_type', $type)
            ->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->where('current_number', '<', DB::raw('range_to'))
            ->first();
    }

    protected function getNextInvoiceNumber(DIANResolution $resolution): string
    {
        return $resolution->prefix . str_pad(
            $resolution->current_number + 1,
            8,
            '0',
            STR_PAD_LEFT
        );
    }

    public function void(Invoice $invoice, string $reason): Invoice
    {
        $invoice->update([
            'status' => InvoiceStatus::VOIDED->value,
            'voided_at' => now(),
            'voided_by' => auth()->id(),
            'void_reason' => $reason,
        ]);

        return $invoice->fresh();
    }
}
