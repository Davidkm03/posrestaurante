<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Branch;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentMethodType;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class RappiService
{
    protected string $baseUrl;
    protected ?string $clientId;
    protected ?string $clientSecret;
    protected ?string $storeId;

    // Dominios por país de Rappi
    const COUNTRY_DOMAINS = [
        'CO' => 'https://api.rappi.com',           // Colombia
        'MX' => 'https://api.mxrappi.com',         // México
        'BR' => 'https://api.rappi.com.br',        // Brasil
        'AR' => 'https://api.rappi.com.ar',        // Argentina
        'CL' => 'https://api.rappi.cl',            // Chile
        'PE' => 'https://api.rappi.pe',            // Perú
        'EC' => 'https://api.rappi.com.ec',        // Ecuador
        'UY' => 'https://api.rappi.com.uy',        // Uruguay
        'CR' => 'https://api.rappi.co.cr',         // Costa Rica
        'DEV' => 'https://api.dev.rappi.com',      // Desarrollo
    ];

    // Tipos de cancelación
    const CANCEL_TYPES = [
        'STORE_CLOSED' => 'Tienda cerrada',
        'OUT_OF_STOCK' => 'Producto agotado',
        'STORE_TOO_BUSY' => 'Tienda muy ocupada',
        'INCORRECT_ADDRESS' => 'Dirección incorrecta',
        'CUSTOMER_REQUEST' => 'Solicitud del cliente',
        'OTHER' => 'Otro motivo',
    ];

    public function __construct()
    {
        $country = config('services.rappi.country', 'CO');
        $this->baseUrl = self::COUNTRY_DOMAINS[$country] ?? self::COUNTRY_DOMAINS['CO'];
        $this->clientId = config('services.rappi.client_id');
        $this->clientSecret = config('services.rappi.client_secret');
        $this->storeId = config('services.rappi.store_id');
    }

    /**
     * Obtener token de autenticación
     */
    public function getAccessToken(): ?string
    {
        $cacheKey = 'rappi_access_token';
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::post("{$this->baseUrl}/restaurants/oauth/v1/token", [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'client_credentials',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'] ?? null;
                $expiresIn = $data['expires_in'] ?? 3600;
                
                if ($token) {
                    Cache::put($cacheKey, $token, now()->addSeconds($expiresIn - 60));
                    return $token;
                }
            }

            Log::error('Rappi: Error obteniendo token', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

        } catch (\Exception $e) {
            Log::error('Rappi: Excepción obteniendo token', [
                'message' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Realizar petición HTTP autenticada
     */
    protected function request(string $method, string $endpoint, array $data = []): ?array
    {
        $token = $this->getAccessToken();
        
        if (!$token) {
            Log::error('Rappi: No se pudo obtener token de acceso');
            return null;
        }

        try {
            $request = Http::withHeaders([
                'x-authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]);

            $url = "{$this->baseUrl}{$endpoint}";

            $response = match(strtoupper($method)) {
                'GET' => $request->get($url, $data),
                'POST' => $request->post($url, $data),
                'PUT' => $request->put($url, $data),
                'DELETE' => $request->delete($url, $data),
                default => throw new \Exception("Método HTTP no soportado: {$method}")
            };

            if ($response->successful()) {
                return $response->json() ?? ['success' => true];
            }

            Log::error('Rappi: Error en petición', [
                'method' => $method,
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Rappi: Excepción en petición', [
                'endpoint' => $endpoint,
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    // =====================================================
    // ÓRDENES
    // =====================================================

    /**
     * Obtener todas las órdenes nuevas
     */
    public function getOrders(): ?array
    {
        return $this->request('GET', '/restaurants/orders/v1/orders');
    }

    /**
     * Obtener órdenes de una tienda específica
     */
    public function getStoreOrders(?string $storeId = null): ?array
    {
        $storeId = $storeId ?? $this->storeId;
        return $this->request('GET', "/restaurants/orders/v1/stores/{$storeId}/orders");
    }

    /**
     * Obtener órdenes en estado SENT
     */
    public function getSentOrders(): ?array
    {
        return $this->request('GET', '/restaurants/orders/v1/orders/status/sent');
    }

    /**
     * Tomar una orden para comenzar preparación
     */
    public function takeOrder(string $orderId, ?string $storeId = null): bool
    {
        $storeId = $storeId ?? $this->storeId;
        $result = $this->request('PUT', "/restaurants/orders/v1/stores/{$storeId}/orders/{$orderId}/take");
        return $result !== null;
    }

    /**
     * Tomar una orden con tiempo de cocción específico
     */
    public function takeOrderWithCookingTime(string $orderId, int $cookingTime, ?string $storeId = null): bool
    {
        $storeId = $storeId ?? $this->storeId;
        $result = $this->request('PUT', "/restaurants/orders/v1/stores/{$storeId}/orders/{$orderId}/cooking_time/{$cookingTime}/take");
        return $result !== null;
    }

    /**
     * Obtener código de confirmación y QR de una orden
     */
    public function getOrderHandoff(string $orderId, ?string $storeId = null): ?array
    {
        $storeId = $storeId ?? $this->storeId;
        return $this->request('GET', "/restaurants/orders/v1/stores/{$storeId}/orders/{$orderId}/handoff");
    }

    /**
     * Confirmar bolsas y bebidas de una orden
     */
    public function confirmBagsDrinks(string $orderId, int $numberOfBags, bool $hasDrinksOutsideBag, ?string $storeId = null): ?array
    {
        $storeId = $storeId ?? $this->storeId;
        return $this->request('POST', "/restaurants/orders/v1/stores/{$storeId}/orders/{$orderId}/bag-drink-confirmation", [
            'number_of_bags' => $numberOfBags,
            'has_drinks_outside_bag' => $hasDrinksOutsideBag,
        ]);
    }

    /**
     * Marcar orden como lista para recoger
     */
    public function markReadyForPickup(string $orderId, ?string $storeId = null): bool
    {
        $storeId = $storeId ?? $this->storeId;
        $result = $this->request('POST', "/restaurants/orders/v1/stores/{$storeId}/orders/{$orderId}/ready-for-pickup");
        return $result !== null;
    }

    /**
     * Rechazar una orden
     */
    public function rejectOrder(string $orderId, string $cancelType, string $description, ?string $storeId = null): bool
    {
        $storeId = $storeId ?? $this->storeId;
        $result = $this->request('PUT', "/restaurants/orders/v1/stores/{$storeId}/orders/{$orderId}/cancel_type/{$cancelType}/reject", [
            'description' => $description,
            'additional_info' => new \stdClass(),
        ]);
        return $result !== null;
    }

    // =====================================================
    // CONVERSIÓN DE ÓRDENES AL SISTEMA LOCAL
    // =====================================================

    /**
     * Convertir orden de Rappi al formato del sistema
     */
    public function convertToLocalOrder(array $rappiOrder, Branch $branch): ?Order
    {
        try {
            $orderDetail = $rappiOrder['order_detail'] ?? [];
            $customer = $rappiOrder['customer'] ?? [];
            $store = $rappiOrder['store'] ?? [];

            // Buscar o crear cliente
            $localCustomer = $this->findOrCreateCustomer($customer);

            // Crear la orden
            $order = Order::create([
                'branch_id' => $branch->id,
                'customer_id' => $localCustomer?->id,
                'user_id' => null, // Orden automática de Rappi
                'order_number' => 'RAPPI-' . ($orderDetail['order_id'] ?? uniqid()),
                'type' => OrderType::DELIVERY,
                'status' => OrderStatus::PENDING,
                'subtotal' => $orderDetail['totals']['total_products'] ?? 0,
                'tax_amount' => 0,
                'discount_amount' => $orderDetail['totals']['total_discounts'] ?? 0,
                'total' => $orderDetail['totals']['total_order'] ?? 0,
                'notes' => $this->buildOrderNotes($orderDetail),
                'source' => 'rappi',
                'external_id' => $orderDetail['order_id'] ?? null,
                'delivery_address' => $this->formatDeliveryAddress($orderDetail['delivery_information'] ?? []),
                'estimated_time' => $orderDetail['cooking_time'] ?? null,
            ]);

            // Agregar items
            foreach ($orderDetail['items'] ?? [] as $item) {
                $this->addOrderItem($order, $item);
            }

            // Recalcular totales
            $order->recalculateTotals();

            Log::info('Rappi: Orden convertida exitosamente', [
                'rappi_order_id' => $orderDetail['order_id'] ?? 'unknown',
                'local_order_id' => $order->id
            ]);

            return $order;

        } catch (\Exception $e) {
            Log::error('Rappi: Error convirtiendo orden', [
                'message' => $e->getMessage(),
                'rappi_order' => $rappiOrder
            ]);
            return null;
        }
    }

    /**
     * Buscar o crear cliente desde datos de Rappi
     */
    protected function findOrCreateCustomer(array $customerData): ?Customer
    {
        if (empty($customerData)) {
            return null;
        }

        $phone = $customerData['phone_number'] ?? null;
        $email = $customerData['email'] ?? null;
        $documentNumber = $customerData['document_number'] ?? null;

        // Buscar por documento, teléfono o email
        $customer = Customer::query()
            ->when($documentNumber, fn($q) => $q->orWhere('document_number', $documentNumber))
            ->when($phone, fn($q) => $q->orWhere('phone', $phone)->orWhere('mobile', $phone))
            ->when($email, fn($q) => $q->orWhere('email', $email))
            ->first();

        if ($customer) {
            return $customer;
        }

        // Crear nuevo cliente
        return Customer::create([
            'first_name' => $customerData['first_name'] ?? 'Cliente',
            'last_name' => $customerData['last_name'] ?? 'Rappi',
            'phone' => $phone,
            'mobile' => $phone,
            'email' => $email,
            'document_number' => $documentNumber,
            'is_active' => true,
        ]);
    }

    /**
     * Agregar item a la orden
     */
    protected function addOrderItem(Order $order, array $item): void
    {
        // Buscar producto por SKU
        $product = Product::where('sku', $item['sku'] ?? '')->first();

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product?->id,
            'product_name' => $item['name'] ?? 'Producto Rappi',
            'quantity' => $item['quantity'] ?? 1,
            'unit_price' => $item['price'] ?? 0,
            'subtotal' => ($item['price'] ?? 0) * ($item['quantity'] ?? 1),
            'notes' => $item['comments'] ?? null,
            'external_id' => $item['id'] ?? null,
        ]);

        // Agregar subitems (toppings/modificadores)
        foreach ($item['subitems'] ?? [] as $subitem) {
            if (is_array($subitem)) {
                // Los subitems se guardan como modificadores en notas
                $orderItem->notes = ($orderItem->notes ? $orderItem->notes . "\n" : '') . 
                    "- {$subitem['name']} (+\${$subitem['price']})";
                $orderItem->save();
            }
        }
    }

    /**
     * Construir notas de la orden
     */
    protected function buildOrderNotes(array $orderDetail): string
    {
        $notes = [];
        
        $notes[] = "📦 Orden Rappi";
        $notes[] = "ID: " . ($orderDetail['order_id'] ?? 'N/A');
        $notes[] = "Método de entrega: " . ($orderDetail['delivery_method'] ?? 'N/A');
        $notes[] = "Método de pago: " . ($orderDetail['payment_method'] ?? 'N/A');
        
        if (isset($orderDetail['billing_information']['name'])) {
            $notes[] = "Facturar a: " . $orderDetail['billing_information']['name'];
        }

        return implode("\n", $notes);
    }

    /**
     * Formatear dirección de entrega
     */
    protected function formatDeliveryAddress(array $deliveryInfo): ?string
    {
        if (empty($deliveryInfo)) {
            return null;
        }

        $parts = array_filter([
            $deliveryInfo['complete_address'] ?? null,
            $deliveryInfo['street_name'] ?? null,
            $deliveryInfo['street_number'] ?? null,
            $deliveryInfo['neighborhood'] ?? null,
            $deliveryInfo['city'] ?? null,
            $deliveryInfo['complement'] ?? null,
        ]);

        return implode(', ', $parts) ?: null;
    }

    // =====================================================
    // SINCRONIZACIÓN
    // =====================================================

    /**
     * Sincronizar órdenes nuevas de Rappi
     */
    public function syncNewOrders(Branch $branch): array
    {
        $results = [
            'synced' => 0,
            'errors' => 0,
            'orders' => []
        ];

        $rappiOrders = $this->getStoreOrders();

        if (!$rappiOrders) {
            return $results;
        }

        foreach ($rappiOrders as $rappiOrder) {
            $externalId = $rappiOrder['order_detail']['order_id'] ?? null;
            
            // Verificar si ya existe
            if ($externalId && Order::where('external_id', $externalId)->exists()) {
                continue;
            }

            $order = $this->convertToLocalOrder($rappiOrder, $branch);
            
            if ($order) {
                $results['synced']++;
                $results['orders'][] = $order->id;
                
                // Tomar la orden automáticamente
                $this->takeOrder($externalId);
            } else {
                $results['errors']++;
            }
        }

        return $results;
    }

    // =====================================================
    // VERIFICACIÓN DE CONEXIÓN
    // =====================================================

    /**
     * Verificar si la conexión con Rappi está activa
     */
    public function testConnection(): array
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return [
                'connected' => false,
                'message' => 'No se pudo obtener token de acceso. Verifica las credenciales.',
            ];
        }

        // Intentar obtener órdenes como prueba
        $orders = $this->getOrders();

        return [
            'connected' => true,
            'message' => 'Conexión exitosa con Rappi',
            'orders_count' => is_array($orders) ? count($orders) : 0,
        ];
    }

    /**
     * Verificar si el servicio está configurado
     */
    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->clientSecret) && !empty($this->storeId);
    }

    /**
     * Obtener configuración actual (sin secretos)
     */
    public function getConfiguration(): array
    {
        return [
            'configured' => $this->isConfigured(),
            'country' => config('services.rappi.country', 'CO'),
            'base_url' => $this->baseUrl,
            'store_id' => $this->storeId,
            'has_client_id' => !empty($this->clientId),
            'has_client_secret' => !empty($this->clientSecret),
        ];
    }
}
