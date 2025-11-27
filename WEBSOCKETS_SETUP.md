# Configuración de WebSockets con Laravel Reverb

## ¿Por qué WebSockets?

En un sistema POS de restaurante, necesitamos actualizaciones en tiempo real para:

1. **Kitchen Display System (KDS)**: Nuevas órdenes aparecen automáticamente
2. **POS - Vista de Mesas**: Estado de mesas actualizado en tiempo real
3. **Dashboard**: Estadísticas de ventas en vivo
4. **Notificaciones**: Alertas de eventos importantes
5. **Sincronización Multi-Usuario**: Varios meseros viendo cambios instantáneamente

## Instalación de Laravel Reverb

### 1. Instalar el paquete

```bash
composer require laravel/reverb
php artisan reverb:install
```

Esto instalará:
- Laravel Reverb (servidor WebSocket)
- Laravel Echo (cliente JavaScript)
- Pusher JS (protocolo compatible)

### 2. Configurar el entorno

Actualiza tu `.env`:

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=posrestaurante
REVERB_APP_KEY=your-app-key-here
REVERB_APP_SECRET=your-app-secret-here
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### 3. Publicar configuración

```bash
php artisan vendor:publish --tag=reverb-config
```

### 4. Habilitar broadcasting en Laravel

Descomenta en `config/app.php`:

```php
App\Providers\BroadcastServiceProvider::class,
```

O crea el provider:

```bash
php artisan make:provider BroadcastServiceProvider
```

## Implementación en el POS

### 1. Eventos para Broadcasting

#### Evento: Nueva Orden

```php
// app/Events/OrderCreated.php
<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('kitchen.' . $this->order->branch_id),
            new Channel('pos.' . $this->order->branch_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.created';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'table' => $this->order->table?->getDisplayName(),
            'items_count' => $this->order->items->count(),
            'total' => $this->order->total,
        ];
    }
}
```

#### Evento: Estado de Mesa Actualizado

```php
// app/Events/TableStatusUpdated.php
<?php

namespace App\Events;

use App\Models\Table;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TableStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Table $table)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('pos.' . $this->table->branch_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'table.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'table_id' => $this->table->id,
            'status' => $this->table->status,
            'occupied_at' => $this->table->occupied_at?->toIso8601String(),
        ];
    }
}
```

### 2. Disparar eventos en los servicios

```php
// app/Services/OrderService.php
use App\Events\OrderCreated;

public function create(array $data): Order
{
    // ... código existente ...
    
    event(new OrderCreated($order));
    
    return $order;
}
```

```php
// app/Models/Table.php
use App\Events\TableStatusUpdated;

public function occupy(Order $order, ?User $waiter = null): void
{
    $this->status = TableStatus::OCCUPIED;
    $this->current_order_id = $order->id;
    $this->occupied_at = now();
    $this->assigned_waiter_id = $waiter?->id ?? $order->user_id;
    $this->save();
    
    event(new TableStatusUpdated($this));
}
```

### 3. Configurar Laravel Echo en el frontend

Actualiza `resources/js/app.js`:

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
```

### 4. Escuchar eventos en las vistas

#### En Kitchen Display (resources/views/kitchen/display/index.blade.php)

```javascript
<script>
// Escuchar nuevas órdenes
window.Echo.channel('kitchen.{{ session("current_branch_id") }}')
    .listen('.order.created', (e) => {
        console.log('Nueva orden:', e);
        
        // Recargar la página o actualizar dinámicamente
        window.location.reload();
        
        // O mejor, con Livewire:
        Livewire.emit('orderCreated', e.order_id);
    })
    .listen('.order.updated', (e) => {
        console.log('Orden actualizada:', e);
        Livewire.emit('orderUpdated', e.order_id);
    });
</script>
```

#### En POS - Vista de Mesas

```javascript
<script>
// Escuchar cambios en mesas
window.Echo.channel('pos.{{ session("current_branch_id") }}')
    .listen('.table.updated', (e) => {
        console.log('Mesa actualizada:', e);
        
        // Con Livewire
        Livewire.emit('refreshTables');
    })
    .listen('.order.created', (e) => {
        console.log('Nueva orden:', e);
        Livewire.emit('orderCreated', e.order_id);
    });
</script>
```

### 5. Integrar con Livewire

#### En el componente TableMap

```php
// app/Livewire/POS/TableMap.php

protected $listeners = [
    'refresh-tables' => '$refresh',
    'refreshTables' => '$refresh',
];
```

#### En el componente Kitchen Display

```php
// app/Livewire/Kitchen/DisplayController.php

protected $listeners = [
    'orderCreated' => 'handleNewOrder',
    'orderUpdated' => 'handleOrderUpdate',
];

public function handleNewOrder($orderId)
{
    // Reproducir sonido de notificación
    $this->dispatch('play-notification-sound');
    
    // Refrescar vista
    $this->dispatch('$refresh');
}
```

## Ejecutar el servidor

### En desarrollo

```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Reverb
php artisan reverb:start

# Terminal 3: Vite (si usas)
npm run dev
```

### En producción

Usar Supervisor para mantener Reverb corriendo:

```ini
[program:reverb]
command=php /path/to/artisan reverb:start
directory=/path/to/project
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/reverb.log
```

## Casos de Uso Específicos

### 1. Notificación de Orden Lista
```php
// Cuando un item está listo en cocina
event(new OrderItemReady($orderItem));
```

### 2. Llamado de Mesero
```php
// Cuando una mesa necesita atención
event(new TableCallWaiter($table));
```

### 3. Actualización de Inventario
```php
// Cuando se agota un producto
event(new ProductOutOfStock($product));
```

### 4. Cambio de Turno
```php
// Cuando se cierra una caja
event(new CashSessionClosed($cashSession));
```

## Testing

Para probar sin configurar Reverb completamente:

```php
// En tu .env de testing
BROADCAST_CONNECTION=log
```

O usar broadcasting fake en tests:

```php
use Illuminate\Support\Facades\Event;

Event::fake([OrderCreated::class]);

// ... tu test ...

Event::assertDispatched(OrderCreated::class);
```

## Alternativas

Si Laravel Reverb no es viable:

1. **Pusher** (servicio en la nube, plan gratuito limitado)
2. **Ably** (similar a Pusher)
3. **Socket.io** con Node.js
4. **Polling** con Livewire (menos eficiente, pero sin configuración extra)

## Beneficios

✅ Actualizaciones instantáneas
✅ Mejor UX para múltiples usuarios
✅ Reduce errores de sincronización
✅ Notificaciones en tiempo real
✅ Sistema más profesional

## Próximos Pasos

1. Instalar Reverb: `composer require laravel/reverb`
2. Crear los eventos de broadcasting
3. Actualizar servicios para disparar eventos
4. Configurar Echo en el frontend
5. Agregar listeners en las vistas clave
6. Probar con múltiples usuarios/pestañas
