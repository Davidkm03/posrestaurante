# ✅ WebSockets Configurado - Guía de Uso

## 🎉 Estado de la Implementación

Laravel Reverb está instalado y configurado correctamente en tu sistema POS.

### ✅ Lo que ya está hecho:

1. **Laravel Reverb instalado** (v1.6.1)
2. **Eventos de broadcasting creados:**
   - `OrderCreated` - Cuando se crea una nueva orden
   - `OrderStatusUpdated` - Cuando cambia el estado de una orden
   - `TableStatusUpdated` - Cuando cambia el estado de una mesa

3. **Eventos integrados en:**
   - `OrderService::create()` - Dispara OrderCreated
   - `OrderService::sendToKitchen()` - Dispara OrderStatusUpdated
   - `OrderService::markReady()` - Dispara OrderStatusUpdated  
   - `Table::occupy()` - Dispara TableStatusUpdated
   - `Table::release()` - Dispara TableStatusUpdated
   - `Table::markAsClean()` - Dispara TableStatusUpdated

4. **Listeners de WebSocket en:**
   - Kitchen Display (`/kitchen/display`) - Recibe nuevas órdenes con sonido
   - POS Main (`/pos`) - Actualiza estado de mesas en tiempo real

5. **Servidor Reverb corriendo en:**
   - Host: `localhost`
   - Puerto: `8080`
   - Esquema: `http`

## 🚀 Cómo Probar

### 1. Servidores necesarios (3 terminales):

```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Reverb (WebSockets)
php artisan reverb:start

# Terminal 3: Vite (opcional, para desarrollo)
npm run dev
```

### 2. Prueba de Kitchen Display:

1. Abre la pantalla de cocina: `http://127.0.0.1:8000/kitchen/display`
2. Abre en otra pestaña/ventana: `http://127.0.0.1:8000/pos`
3. En el POS:
   - Selecciona una mesa
   - Agrega productos
   - Haz clic en "Enviar a Cocina"
4. **En la pantalla de cocina verás:**
   - ✅ La orden aparece automáticamente
   - 🔊 Sonido de notificación
   - 🔄 Actualización sin recargar manualmente

### 3. Prueba de Actualización de Mesas:

1. Abre dos pestañas del POS: `http://127.0.0.1:8000/pos`
2. En la pestaña 1: Selecciona una mesa (cambia a "Ocupada")
3. **En la pestaña 2 verás:**
   - ✅ El estado de la mesa se actualiza automáticamente
   - 🔄 La vista se refresca sola

## 🎯 Eventos Disponibles

### Canal: `kitchen.{branch_id}`

#### Evento: `order.created`
```javascript
{
    order_id: 123,
    order_number: "ORD-001",
    table: "Mesa 5",
    table_id: 5,
    type: "dine_in",
    items_count: 3,
    total: 45000,
    created_at: "2025-11-24T12:00:00Z"
}
```

#### Evento: `order.status.updated`
```javascript
{
    order_id: 123,
    order_number: "ORD-001",
    old_status: "pending",
    new_status: "in_preparation",
    table_id: 5,
    updated_at: "2025-11-24T12:05:00Z"
}
```

### Canal: `pos.{branch_id}`

#### Evento: `table.status.updated`
```javascript
{
    table_id: 5,
    table_number: "5",
    status: "occupied",
    current_order_id: 123,
    occupied_at: "2025-11-24T12:00:00Z",
    updated_at: "2025-11-24T12:00:00Z"
}
```

## 📝 Debugging

### Ver mensajes en tiempo real:

Abre la consola del navegador (F12) y verás:
```
Nueva orden recibida: {order_id: 123, ...}
Mesa actualizada: {table_id: 5, ...}
```

### Verificar que Reverb está corriendo:

```bash
# Ver procesos de Reverb
ps aux | grep reverb

# Ver logs de Reverb
tail -f storage/logs/laravel.log
```

### Verificar conexión de WebSocket:

En la consola del navegador:
```javascript
console.log(window.Echo);
// Debe mostrar un objeto Echo, no undefined
```

## 🔧 Configuración Adicional

### Para usar en producción:

1. **Cambiar a HTTPS:**
```env
REVERB_SCHEME=https
VITE_REVERB_SCHEME=https
```

2. **Usar Supervisor para mantener Reverb corriendo:**
```ini
[program:reverb]
command=php /path/to/artisan reverb:start
directory=/path/to/posrestaurante
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/reverb.log
```

3. **Configurar Nginx/Apache como proxy:**
```nginx
location /reverb {
    proxy_pass http://localhost:8080;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
}
```

## 🎨 Personalizar Notificaciones

### Cambiar el sonido de notificación:

En `kitchen/display/index.blade.php`, modifica `playNotificationSound()`:

```javascript
function playNotificationSound() {
    // Usar un archivo de audio personalizado
    const audio = new Audio('/sounds/notification.mp3');
    audio.play();
}
```

### Agregar animaciones:

```javascript
.listen('.order.created', (e) => {
    // Agregar animación de entrada
    const orderCard = document.querySelector(`[data-order-id="${e.order_id}"]`);
    if (orderCard) {
        orderCard.classList.add('animate-bounce');
    }
});
```

## 📊 Monitoreo

### Ver estadísticas de Reverb:

Reverb expone métricas en: `http://localhost:8080/health`

### Conexiones activas:

```bash
# Ver conexiones WebSocket activas
php artisan reverb:status
```

## 🆘 Solución de Problemas

### ❌ "Echo is not defined"

**Problema:** Laravel Echo no está inicializado.

**Solución:**
```bash
# Reconstruir assets
npm run build
# Limpiar caché del navegador (Ctrl+Shift+R)
```

### ❌ "Connection refused"

**Problema:** Reverb no está corriendo.

**Solución:**
```bash
# Iniciar Reverb
php artisan reverb:start
```

### ❌ No se reciben eventos

**Problema:** Branch ID incorrecto o sesión no iniciada.

**Solución:**
- Verificar que estés autenticado
- Verificar que haya una caja abierta
- Revisar en la consola del navegador el branch_id

## 🚀 Próximas Mejoras

### Eventos adicionales que puedes crear:

1. **ProductOutOfStock** - Notificar cuando se agota un producto
2. **TableCallWaiter** - Llamar al mesero desde una mesa
3. **OrderItemReady** - Un item específico está listo
4. **CashSessionAlert** - Alertas de caja (faltantes/sobrantes)
5. **ReservationReminder** - Recordatorio de reservaciones

### Integración con Livewire:

Para actualizaciones más granulares sin recargar:

```php
// En tu componente Livewire
protected $listeners = ['echo:kitchen.1,.order.created' => 'handleNewOrder'];

public function handleNewOrder($event)
{
    // Actualizar solo lo necesario
    $this->orders = Order::pending()->get();
}
```

## 📚 Recursos

- [Laravel Reverb Docs](https://laravel.com/docs/11.x/reverb)
- [Laravel Echo Docs](https://laravel.com/docs/11.x/broadcasting#client-side-installation)
- [Broadcasting Events](https://laravel.com/docs/11.x/broadcasting)

## ✅ Checklist de Implementación

- [x] Instalar Laravel Reverb
- [x] Configurar `.env`
- [x] Crear eventos de broadcasting
- [x] Integrar eventos en servicios
- [x] Agregar listeners en vistas
- [x] Iniciar servidor Reverb
- [ ] Probar en múltiples pestañas
- [ ] Configurar Supervisor (producción)
- [ ] Agregar más eventos según necesidad
- [ ] Integrar con Livewire para updates granulares

---

**¡WebSockets está listo para usar!** 🎉

Prueba creando una orden en el POS mientras tienes abierta la pantalla de cocina, y verás la magia del tiempo real.
