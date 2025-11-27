<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\Factus\FactusAuthService;
use App\Services\Factus\FactusClientService;
use App\Services\Factus\FactusInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FactusController extends Controller
{
    protected FactusAuthService $authService;
    protected FactusInvoiceService $invoiceService;
    protected FactusClientService $clientService;

    public function __construct(
        FactusAuthService $authService,
        FactusInvoiceService $invoiceService,
        FactusClientService $clientService
    ) {
        $this->authService = $authService;
        $this->invoiceService = $invoiceService;
        $this->clientService = $clientService;
    }

    /**
     * Probar conexión con Factus
     */
    public function test()
    {
        try {
            $result = $this->clientService->testConnection();

            if ($result['success']) {
                return redirect()
                    ->route('admin.settings.integrations')
                    ->with('success', '✅ Conexión exitosa con Factus. ' . ($result['company']['company']['name'] ?? ''));
            }

            return redirect()
                ->route('admin.settings.integrations')
                ->with('error', '❌ Error de conexión: ' . ($result['message'] ?? 'Error desconocido'));

        } catch (\Exception $e) {
            Log::error('Factus: Error en test de conexión', ['error' => $e->getMessage()]);
            return redirect()
                ->route('admin.settings.integrations')
                ->with('error', '❌ Error: ' . $e->getMessage());
        }
    }

    /**
     * Sincronizar catálogos de Factus
     */
    public function sync()
    {
        try {
            $results = $this->clientService->syncCatalogs();

            $synced = [];
            foreach ($results as $key => $result) {
                if ($result['success']) {
                    $synced[] = str_replace('_', ' ', ucfirst($key)) . " ({$result['count']})";
                }
            }

            if (!empty($synced)) {
                return redirect()
                    ->route('admin.settings.integrations')
                    ->with('success', '✅ Sincronizado: ' . implode(', ', $synced));
            }

            return redirect()
                ->route('admin.settings.integrations')
                ->with('warning', '⚠️ No se sincronizaron catálogos');

        } catch (\Exception $e) {
            Log::error('Factus: Error en sincronización', ['error' => $e->getMessage()]);
            return redirect()
                ->route('admin.settings.integrations')
                ->with('error', '❌ Error: ' . $e->getMessage());
        }
    }

    /**
     * Cerrar sesión (invalidar token)
     */
    public function logout()
    {
        $this->authService->logout();

        return redirect()
            ->route('admin.settings.integrations')
            ->with('success', 'Sesión de Factus cerrada.');
    }

    /**
     * Crear factura electrónica desde una factura existente
     */
    public function createInvoice(Invoice $invoice)
    {
        try {
            $result = $this->invoiceService->sendInvoice($invoice);

            if ($result['success']) {
                return redirect()
                    ->back()
                    ->with('success', '✅ Factura electrónica creada exitosamente');
            }

            return redirect()
                ->back()
                ->with('error', '❌ Error: ' . ($result['error'] ?? 'Error desconocido'));

        } catch (\Exception $e) {
            Log::error('Factus: Error creando factura', ['error' => $e->getMessage()]);
            return redirect()
                ->back()
                ->with('error', '❌ Error: ' . $e->getMessage());
        }
    }

    /**
     * Descargar PDF de factura
     */
    public function downloadPdf(string $invoiceId)
    {
        try {
            $result = $this->invoiceService->downloadPdf($invoiceId);

            if (!$result['success']) {
                return redirect()->back()->with('error', $result['error']);
            }

            $pdfContent = base64_decode($result['pdf_base64']);

            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $result['file_name'] . '"',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error descargando PDF: ' . $e->getMessage());
        }
    }

    /**
     * Descargar XML de factura
     */
    public function downloadXml(string $invoiceId)
    {
        try {
            $result = $this->invoiceService->downloadXml($invoiceId);

            if (!$result['success']) {
                return redirect()->back()->with('error', $result['error']);
            }

            $xmlContent = base64_decode($result['xml_base64']);

            return response($xmlContent, 200, [
                'Content-Type' => 'application/xml',
                'Content-Disposition' => 'attachment; filename="' . $result['file_name'] . '"',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error descargando XML: ' . $e->getMessage());
        }
    }

    /**
     * Ver estado del token
     */
    public function tokenStatus()
    {
        return response()->json([
            'has_valid_token' => $this->authService->hasValidToken(),
            'token_info' => $this->authService->getTokenInfo(),
        ]);
    }

    /**
     * Ejemplo de prueba - crear factura de prueba
     */
    public function testInvoice()
    {
        $payload = [
            'numbering_range_id' => 8, // ID del rango de numeración en Factus
            'reference_code' => 'TEST-' . time(),
            'observation' => 'Factura de prueba generada desde el POS',
            'payment_method_code' => '10', // Efectivo
            
            'customer' => [
                'identification_document_id' => 1, // CC
                'identification' => '222222222222',
                'company_name' => null,
                'names' => 'Consumidor',
                'surnames' => 'Final',
                'address' => 'Bogotá',
                'email' => 'test@example.com',
                'phone' => '3001234567',
                'legal_organization_id' => 2, // Persona natural
                'tribute_id' => 21, // No responsable de IVA
                'municipality_id' => 1006, // Bogotá
            ],

            'items' => [
                [
                    'code_reference' => 'PROD-001',
                    'name' => 'Producto de prueba',
                    'quantity' => 1,
                    'discount_rate' => 0,
                    'price' => 10000,
                    'tax_rate' => 19,
                    'unit_measure_id' => 70, // Unidad
                    'standard_code_id' => 1,
                    'is_excluded' => 0,
                    'tribute_id' => 1, // IVA
                ],
            ],
        ];

        $result = $this->invoiceService->createInvoice($payload);

        return response()->json($result);
    }
}
