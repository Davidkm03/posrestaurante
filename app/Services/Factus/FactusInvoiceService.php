<?php

namespace App\Services\Factus;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Invoice;

class FactusInvoiceService
{
    protected FactusAuthService $auth;

    public function __construct(FactusAuthService $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Headers comunes para todas las peticiones
     */
    private function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->auth->getToken(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Crear una factura electrónica
     */
    public function createInvoice(array $payload): array
    {
        Log::info('Factus: Creando factura...', ['payload' => $payload]);

        $response = Http::withHeaders($this->headers())
            ->timeout(config('factus.timeout', 30))
            ->post(config('factus.base_url') . '/v1/bills/validate', $payload);

        $result = $response->json();

        if (!$response->successful()) {
            Log::error('Factus: Error creando factura', [
                'status' => $response->status(),
                'response' => $result
            ]);
            return [
                'success' => false,
                'error' => $result['message'] ?? 'Error desconocido',
                'errors' => $result['errors'] ?? [],
                'data' => $result
            ];
        }

        Log::info('Factus: Factura creada exitosamente', ['response' => $result]);

        return [
            'success' => true,
            'data' => $result
        ];
    }

    /**
     * Obtener una factura por su ID o número
     */
    public function getInvoice(string $invoiceId): array
    {
        $response = Http::withHeaders($this->headers())
            ->timeout(config('factus.timeout', 30))
            ->get(config('factus.base_url') . "/v1/bills/show/{$invoiceId}");

        $result = $response->json();

        if (!$response->successful()) {
            return [
                'success' => false,
                'error' => $result['message'] ?? 'Factura no encontrada',
                'data' => $result
            ];
        }

        return [
            'success' => true,
            'data' => $result
        ];
    }

    /**
     * Listar facturas con filtros
     */
    public function listInvoices(array $filters = []): array
    {
        $query = http_build_query($filters);
        $url = config('factus.base_url') . '/v1/bills' . ($query ? "?{$query}" : '');

        $response = Http::withHeaders($this->headers())
            ->timeout(config('factus.timeout', 30))
            ->get($url);

        return [
            'success' => $response->successful(),
            'data' => $response->json()
        ];
    }

    /**
     * Descargar PDF de una factura
     */
    public function downloadPdf(string $invoiceId): array
    {
        $response = Http::withHeaders($this->headers())
            ->timeout(config('factus.timeout', 30))
            ->get(config('factus.base_url') . "/v1/bills/download-pdf/{$invoiceId}");

        if (!$response->successful()) {
            return [
                'success' => false,
                'error' => 'No se pudo descargar el PDF'
            ];
        }

        // La respuesta viene en base64
        $data = $response->json();
        
        return [
            'success' => true,
            'pdf_base64' => $data['data']['pdf_base_64_encoded'] ?? null,
            'file_name' => $data['data']['file_name'] ?? 'factura.pdf'
        ];
    }

    /**
     * Descargar XML de una factura
     */
    public function downloadXml(string $invoiceId): array
    {
        $response = Http::withHeaders($this->headers())
            ->timeout(config('factus.timeout', 30))
            ->get(config('factus.base_url') . "/v1/bills/download-xml/{$invoiceId}");

        if (!$response->successful()) {
            return [
                'success' => false,
                'error' => 'No se pudo descargar el XML'
            ];
        }

        $data = $response->json();

        return [
            'success' => true,
            'xml_base64' => $data['data']['xml_base_64_encoded'] ?? null,
            'file_name' => $data['data']['file_name'] ?? 'factura.xml'
        ];
    }

    /**
     * Crear nota crédito
     */
    public function createCreditNote(array $payload): array
    {
        Log::info('Factus: Creando nota crédito...', ['payload' => $payload]);

        $response = Http::withHeaders($this->headers())
            ->timeout(config('factus.timeout', 30))
            ->post(config('factus.base_url') . '/v1/credit-notes/validate', $payload);

        $result = $response->json();

        if (!$response->successful()) {
            Log::error('Factus: Error creando nota crédito', [
                'status' => $response->status(),
                'response' => $result
            ]);
            return [
                'success' => false,
                'error' => $result['message'] ?? 'Error desconocido',
                'data' => $result
            ];
        }

        return [
            'success' => true,
            'data' => $result
        ];
    }

    /**
     * Crear nota débito
     */
    public function createDebitNote(array $payload): array
    {
        Log::info('Factus: Creando nota débito...', ['payload' => $payload]);

        $response = Http::withHeaders($this->headers())
            ->timeout(config('factus.timeout', 30))
            ->post(config('factus.base_url') . '/v1/debit-notes/validate', $payload);

        $result = $response->json();

        if (!$response->successful()) {
            return [
                'success' => false,
                'error' => $result['message'] ?? 'Error desconocido',
                'data' => $result
            ];
        }

        return [
            'success' => true,
            'data' => $result
        ];
    }

    /**
     * Convertir una factura del sistema a formato Factus
     */
    public function prepareInvoicePayload(Invoice $invoice): array
    {
        $customer = $invoice->customer;
        $items = $invoice->items;

        // Mapear tipo de documento
        $documentTypes = [
            'CC' => 1,  // Cédula de ciudadanía
            'CE' => 2,  // Cédula de extranjería
            'NIT' => 3, // NIT
            'PA' => 4,  // Pasaporte
            'TI' => 5,  // Tarjeta de identidad
        ];

        $payload = [
            'numbering_range_id' => config('factus.numbering_range_id', 1),
            'reference_code' => $invoice->invoice_number,
            'observation' => $invoice->notes ?? '',
            'payment_method_code' => $this->mapPaymentMethod($invoice),
            
            // Datos del cliente
            'customer' => [
                'identification_document_id' => $documentTypes[$customer->document_type ?? 'CC'] ?? 1,
                'identification' => $customer->document_number ?? '222222222222',
                'company_name' => $customer->company_name ?? null,
                'names' => $customer->first_name ?? 'Consumidor',
                'surnames' => $customer->last_name ?? 'Final',
                'address' => $customer->address ?? 'N/A',
                'email' => $customer->email ?? null,
                'phone' => $customer->phone ?? null,
                'legal_organization_id' => ($customer->document_type === 'NIT') ? 1 : 2, // 1: Persona jurídica, 2: Natural
                'tribute_id' => ($customer->document_type === 'NIT') ? 1 : 21, // 1: IVA, 21: No responsable
                'municipality_id' => $customer->municipality_id ?? 1006, // Bogotá por defecto
            ],

            // Items de la factura
            'items' => $this->prepareItems($items),
        ];

        return $payload;
    }

    /**
     * Preparar items para Factus
     */
    private function prepareItems($items): array
    {
        $factusItems = [];

        foreach ($items as $item) {
            $factusItems[] = [
                'code_reference' => $item->product->sku ?? $item->product_id,
                'name' => $item->product_name ?? $item->product->name,
                'quantity' => $item->quantity,
                'discount_rate' => $item->discount_percentage ?? 0,
                'price' => $item->unit_price,
                'tax_rate' => $this->getTaxRate($item),
                'unit_measure_id' => 70, // Unidad por defecto
                'standard_code_id' => 1, // Código estándar de adopción del contribuyente
                'is_excluded' => $this->isExcluded($item) ? 1 : 0,
                'tribute_id' => 1, // IVA
            ];
        }

        return $factusItems;
    }

    /**
     * Obtener tasa de impuesto
     */
    private function getTaxRate($item): float
    {
        // Si el producto tiene IVA configurado
        if (isset($item->product->tax_rate)) {
            return $item->product->tax_rate;
        }

        // Por defecto 19% para productos gravados
        return 19.00;
    }

    /**
     * Verificar si el producto está excluido de IVA
     */
    private function isExcluded($item): bool
    {
        return ($item->product->tax_rate ?? 19) == 0;
    }

    /**
     * Mapear método de pago al código de Factus
     */
    private function mapPaymentMethod(Invoice $invoice): string
    {
        // Códigos DIAN para métodos de pago
        // 1 = Instrumento no definido
        // 2 = Crédito ACH
        // 10 = Efectivo
        // 47 = Transferencia
        // 48 = Tarjeta crédito
        // 49 = Tarjeta débito

        $payment = $invoice->order?->payments?->first();
        
        if (!$payment) {
            return '10'; // Efectivo por defecto
        }

        $methodMap = [
            'cash' => '10',
            'credit_card' => '48',
            'debit_card' => '49',
            'transfer' => '47',
            'nequi' => '47',
            'daviplata' => '47',
        ];

        return $methodMap[$payment->paymentMethod?->type?->value ?? 'cash'] ?? '10';
    }

    /**
     * Enviar factura existente a Factus
     */
    public function sendInvoice(Invoice $invoice): array
    {
        $payload = $this->prepareInvoicePayload($invoice);
        $result = $this->createInvoice($payload);

        if ($result['success']) {
            // Actualizar la factura con los datos de DIAN
            $dianData = $result['data']['data']['bill'] ?? [];
            
            $invoice->update([
                'dian_uuid' => $dianData['uuid'] ?? null,
                'dian_cufe' => $dianData['cufe'] ?? null,
                'dian_qr' => $dianData['qr_code'] ?? null,
                'dian_status' => 'validated',
                'dian_response' => $result['data'],
                'dian_validated_at' => now(),
            ]);
        }

        return $result;
    }
}
