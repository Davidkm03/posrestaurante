<?php

namespace App\Services\Factus;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FactusClientService
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
     * Obtener rangos de numeración disponibles
     */
    public function getNumberingRanges(): array
    {
        $response = Http::withHeaders($this->headers())
            ->timeout(config('factus.timeout', 30))
            ->get(config('factus.base_url') . '/v1/numbering-ranges');

        return [
            'success' => $response->successful(),
            'data' => $response->json()
        ];
    }

    /**
     * Obtener municipios de Colombia
     */
    public function getMunicipalities(): array
    {
        $response = Http::withHeaders($this->headers())
            ->timeout(config('factus.timeout', 30))
            ->get(config('factus.base_url') . '/v1/municipalities');

        return [
            'success' => $response->successful(),
            'data' => $response->json()
        ];
    }

    /**
     * Obtener tipos de documentos de identidad
     */
    public function getIdentificationDocuments(): array
    {
        $response = Http::withHeaders($this->headers())
            ->timeout(config('factus.timeout', 30))
            ->get(config('factus.base_url') . '/v1/identification-documents');

        return [
            'success' => $response->successful(),
            'data' => $response->json()
        ];
    }

    /**
     * Obtener tributos disponibles
     */
    public function getTributes(): array
    {
        $response = Http::withHeaders($this->headers())
            ->timeout(config('factus.timeout', 30))
            ->get(config('factus.base_url') . '/v1/tributes');

        return [
            'success' => $response->successful(),
            'data' => $response->json()
        ];
    }

    /**
     * Obtener unidades de medida
     */
    public function getUnitMeasures(): array
    {
        $response = Http::withHeaders($this->headers())
            ->timeout(config('factus.timeout', 30))
            ->get(config('factus.base_url') . '/v1/measurement-units');

        return [
            'success' => $response->successful(),
            'data' => $response->json()
        ];
    }

    /**
     * Obtener métodos de pago DIAN
     */
    public function getPaymentMethods(): array
    {
        $response = Http::withHeaders($this->headers())
            ->timeout(config('factus.timeout', 30))
            ->get(config('factus.base_url') . '/v1/payment-methods');

        return [
            'success' => $response->successful(),
            'data' => $response->json()
        ];
    }

    /**
     * Obtener información de la empresa configurada en Factus
     */
    public function getCompanyInfo(): array
    {
        $response = Http::withHeaders($this->headers())
            ->timeout(config('factus.timeout', 30))
            ->get(config('factus.base_url') . '/v1/profile');

        return [
            'success' => $response->successful(),
            'data' => $response->json()
        ];
    }

    /**
     * Probar conexión con Factus
     */
    public function testConnection(): array
    {
        try {
            // Intentar obtener token
            $token = $this->auth->getToken();
            
            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'No se pudo obtener token de autenticación'
                ];
            }

            // Intentar obtener info de la empresa
            $companyInfo = $this->getCompanyInfo();
            
            if (!$companyInfo['success']) {
                return [
                    'success' => false,
                    'message' => 'Token válido pero no se pudo obtener información de la empresa'
                ];
            }

            return [
                'success' => true,
                'message' => 'Conexión exitosa con Factus',
                'company' => $companyInfo['data']['data'] ?? null,
                'token_info' => $this->auth->getTokenInfo()
            ];

        } catch (\Exception $e) {
            Log::error('Factus: Error probando conexión', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error de conexión: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Sincronizar catálogos de referencia de Factus
     */
    public function syncCatalogs(): array
    {
        $results = [];

        // Sincronizar rangos de numeración
        $ranges = $this->getNumberingRanges();
        $results['numbering_ranges'] = [
            'success' => $ranges['success'],
            'count' => count($ranges['data']['data'] ?? [])
        ];

        // Sincronizar municipios
        $municipalities = $this->getMunicipalities();
        $results['municipalities'] = [
            'success' => $municipalities['success'],
            'count' => count($municipalities['data']['data'] ?? [])
        ];

        // Sincronizar tipos de documento
        $documents = $this->getIdentificationDocuments();
        $results['identification_documents'] = [
            'success' => $documents['success'],
            'count' => count($documents['data']['data'] ?? [])
        ];

        // Sincronizar tributos
        $tributes = $this->getTributes();
        $results['tributes'] = [
            'success' => $tributes['success'],
            'count' => count($tributes['data']['data'] ?? [])
        ];

        // Sincronizar unidades de medida
        $units = $this->getUnitMeasures();
        $results['unit_measures'] = [
            'success' => $units['success'],
            'count' => count($units['data']['data'] ?? [])
        ];

        return $results;
    }
}
