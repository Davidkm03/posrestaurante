<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RappiService;
use App\Models\Branch;
use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RappiController extends Controller
{
    protected RappiService $rappiService;

    public function __construct(RappiService $rappiService)
    {
        $this->rappiService = $rappiService;
    }

    /**
     * Conectar con Rappi - guardar credenciales
     */
    public function connect(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'store_id' => 'required|string',
            'domain' => 'required|string',
        ]);

        try {
            // Guardar en archivo .env
            $this->updateEnvFile([
                'RAPPI_CLIENT_ID' => $validated['client_id'],
                'RAPPI_CLIENT_SECRET' => $validated['client_secret'],
                'RAPPI_STORE_ID' => $validated['store_id'],
                'RAPPI_DOMAIN' => $validated['domain'],
            ]);

            // Crear una instancia temporal del servicio con las nuevas credenciales
            config([
                'services.rappi.client_id' => $validated['client_id'],
                'services.rappi.client_secret' => $validated['client_secret'],
                'services.rappi.store_id' => $validated['store_id'],
                'services.rappi.domain' => $validated['domain'],
            ]);
            
            // Probar la conexión
            $testResult = $this->rappiService->testConnection();
            
            if (!$testResult['connected']) {
                return back()->with('error', 'No se pudo autenticar con Rappi. Verifica las credenciales.');
            }

            Cache::put('rappi_connected', true, now()->addYear());
            Cache::put('rappi_last_sync', now()->format('d/m/Y H:i'), now()->addDays(30));

            return back()->with('success', 'Conectado con Rappi exitosamente.');
            
        } catch (\Exception $e) {
            Log::error('Error conectando con Rappi', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error al conectar con Rappi: ' . $e->getMessage());
        }
    }

    /**
     * Desconectar de Rappi
     */
    public function disconnect()
    {
        try {
            // Limpiar credenciales del .env
            $this->updateEnvFile([
                'RAPPI_CLIENT_ID' => '',
                'RAPPI_CLIENT_SECRET' => '',
                'RAPPI_STORE_ID' => '',
                'RAPPI_DOMAIN' => '',
            ]);

            Cache::forget('rappi_connected');
            Cache::forget('rappi_last_sync');
            Cache::forget('rappi_access_token');

            return back()->with('success', 'Desconectado de Rappi exitosamente.');
            
        } catch (\Exception $e) {
            Log::error('Error desconectando de Rappi', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error al desconectar de Rappi.');
        }
    }

    /**
     * Sincronizar órdenes de Rappi
     */
    public function sync()
    {
        try {
            $branch = Branch::first();
            
            if (!$branch) {
                return back()->with('error', 'No hay sucursal configurada.');
            }

            $result = $this->rappiService->syncNewOrders($branch);

            Cache::put('rappi_last_sync', now()->format('d/m/Y H:i'), now()->addDays(30));

            return back()->with('success', "Sincronización completada: {$result['synced']} órdenes importadas" . ($result['errors'] > 0 ? ", {$result['errors']} errores" : ''));
            
        } catch (\Exception $e) {
            Log::error('Error sincronizando órdenes de Rappi', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error al sincronizar órdenes: ' . $e->getMessage());
        }
    }

    /**
     * Sincronizar menú a Rappi
     */
    public function syncMenu()
    {
        // Por ahora solo mostramos mensaje informativo
        // La sincronización de menú requiere endpoints específicos de Rappi
        return back()->with('success', 'Funcionalidad de sincronización de menú próximamente disponible.');
    }

    /**
     * Aceptar una orden de Rappi
     */
    public function acceptOrder(Request $request, Order $order)
    {
        try {
            $cookingTime = $request->get('cooking_time', 20);
            
            $success = $this->rappiService->takeOrderWithCookingTime(
                $order->external_id,
                (int) $cookingTime
            );
            
            if ($success) {
                $order->status = OrderStatus::IN_PREPARATION;
                $order->save();
                
                return back()->with('success', 'Orden aceptada exitosamente.');
            }
            
            return back()->with('error', 'No se pudo aceptar la orden en Rappi.');
            
        } catch (\Exception $e) {
            Log::error('Error aceptando orden Rappi', ['order' => $order->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Error al aceptar la orden.');
        }
    }

    /**
     * Rechazar una orden de Rappi
     */
    public function rejectOrder(Request $request, Order $order)
    {
        $validated = $request->validate([
            'cancel_type' => 'required|string',
            'description' => 'required|string|max:500',
        ]);

        try {
            $success = $this->rappiService->rejectOrder(
                $order->external_id,
                $validated['cancel_type'],
                $validated['description']
            );
            
            if ($success) {
                $order->status = OrderStatus::CANCELLED;
                $order->notes = ($order->notes ? $order->notes . "\n" : '') . 
                    "Rechazada: {$validated['description']}";
                $order->save();
                
                return back()->with('success', 'Orden rechazada exitosamente.');
            }
            
            return back()->with('error', 'No se pudo rechazar la orden en Rappi.');
            
        } catch (\Exception $e) {
            Log::error('Error rechazando orden Rappi', ['order' => $order->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Error al rechazar la orden.');
        }
    }

    /**
     * Marcar orden como lista para recoger
     */
    public function readyForPickup(Order $order)
    {
        try {
            $success = $this->rappiService->markReadyForPickup($order->external_id);
            
            if ($success) {
                $order->status = OrderStatus::READY;
                $order->save();
                
                return back()->with('success', 'Orden marcada como lista para recoger.');
            }
            
            return back()->with('error', 'No se pudo actualizar el estado en Rappi.');
            
        } catch (\Exception $e) {
            Log::error('Error marcando orden lista Rappi', ['order' => $order->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Error al actualizar la orden.');
        }
    }

    /**
     * Webhook para recibir notificaciones de Rappi
     */
    public function webhook(Request $request)
    {
        Log::info('Rappi Webhook recibido', [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
        ]);

        $event = $request->get('event') ?? $request->get('type');
        $data = $request->get('data') ?? $request->all();

        switch ($event) {
            case 'order.created':
            case 'new_order':
                $this->handleNewOrder($data);
                break;
            
            case 'order.cancelled':
            case 'order_cancelled':
                $this->handleOrderCancelled($data);
                break;

            case 'order.updated':
                $this->handleOrderUpdated($data);
                break;

            default:
                Log::info('Rappi: Evento de webhook no manejado', ['event' => $event]);
        }

        return response()->json(['received' => true]);
    }

    /**
     * Manejar nueva orden desde webhook
     */
    protected function handleNewOrder(array $data): void
    {
        $branch = Branch::first();
        
        if (!$branch) {
            Log::error('Rappi Webhook: No hay sucursal configurada');
            return;
        }

        $order = $this->rappiService->convertToLocalOrder($data, $branch);
        
        if ($order) {
            Log::info('Rappi Webhook: Nueva orden procesada', ['order_id' => $order->id]);
        }
    }

    /**
     * Manejar orden cancelada desde webhook
     */
    protected function handleOrderCancelled(array $data): void
    {
        $externalId = $data['order_id'] ?? $data['order_detail']['order_id'] ?? null;
        
        if (!$externalId) {
            return;
        }

        $order = Order::where('external_id', $externalId)->first();
        
        if ($order) {
            $order->status = OrderStatus::CANCELLED;
            $order->notes = ($order->notes ? $order->notes . "\n" : '') . 
                "Cancelada por Rappi: " . ($data['reason'] ?? 'Sin motivo');
            $order->save();

            Log::info('Rappi Webhook: Orden cancelada', ['order_id' => $order->id]);
        }
    }

    /**
     * Manejar orden actualizada desde webhook
     */
    protected function handleOrderUpdated(array $data): void
    {
        $externalId = $data['order_id'] ?? $data['order_detail']['order_id'] ?? null;
        
        if (!$externalId) {
            return;
        }

        $order = Order::where('external_id', $externalId)->first();
        
        if ($order) {
            Log::info('Rappi Webhook: Orden actualizada', ['order_id' => $order->id, 'data' => $data]);
        }
    }

    /**
     * Actualizar archivo .env
     */
    protected function updateEnvFile(array $values): void
    {
        $envFile = base_path('.env');
        
        if (!file_exists($envFile)) {
            return;
        }

        $envContent = file_get_contents($envFile);

        foreach ($values as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}=\"{$value}\"";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        file_put_contents($envFile, $envContent);

        // Limpiar cache de config
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }
}
