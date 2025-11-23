# POS Restaurante - Arquitectura del Sistema

## Requisitos Técnicos

| Componente | Versión |
|------------|---------|
| PHP | 8.3+ |
| Laravel | 11.x |
| MySQL | 8.0+ |
| Node.js | 20.x LTS |
| Tailwind CSS | 3.x |
| Livewire | 3.x |

---

## Estructura de Directorios

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/           # Controladores del panel administrativo
│   │   ├── POS/             # Controladores del punto de venta
│   │   ├── Kitchen/         # Controladores de cocina
│   │   └── Api/             # Controladores API REST
│   ├── Middleware/
│   ├── Requests/            # Form Requests por módulo
│   │   ├── Product/
│   │   ├── Order/
│   │   ├── Table/
│   │   └── User/
│   └── Resources/           # API Resources
│       ├── ProductResource.php
│       ├── OrderResource.php
│       └── ...
├── Models/                  # Un archivo por modelo
├── Services/                # Lógica de negocio
│   ├── OrderService.php
│   ├── PaymentService.php
│   ├── InventoryService.php
│   ├── ReportService.php
│   └── PrinterService.php
├── Repositories/            # Patrón Repository
│   ├── Contracts/           # Interfaces
│   └── Eloquent/            # Implementaciones
├── Events/
├── Listeners/
├── Jobs/                    # Colas para tareas pesadas
├── Enums/                   # PHP 8.3 Enums
│   ├── OrderStatus.php
│   ├── PaymentMethod.php
│   ├── UserRole.php
│   └── TableStatus.php
└── Traits/                  # Traits reutilizables
```

---

## Módulos del Sistema

### 1. Módulo de Autenticación
- Login/Logout
- Roles: Admin, Cajero, Mesero, Cocina
- Permisos con Spatie/Permission
- PIN rápido para cambio de usuario en POS

### 2. Módulo de Productos
- CRUD de productos
- Categorías y subcategorías
- Modificadores/Extras (queso extra, sin cebolla, etc.)
- Combos y promociones
- Control de disponibilidad
- Imágenes de productos

### 3. Módulo de Mesas
- Mapa visual del restaurante
- Estados: Libre, Ocupada, Reservada, Por limpiar
- Unir/dividir mesas
- Asignación de meseros
- Transferencia de mesa

### 4. Módulo de Órdenes
- Crear/editar órdenes
- Órdenes por mesa o para llevar
- División de cuenta
- Notas especiales por item
- Estados: Pendiente, En preparación, Listo, Entregado, Pagado
- Historial de cambios (audit)

### 5. Módulo de Cocina (KDS)
- Pantalla de pedidos pendientes
- Marcar items como preparados
- Tiempos de preparación
- Alertas de demora
- Priorización de órdenes

### 6. Módulo de Pagos
- Efectivo, tarjeta, transferencia
- Pagos parciales/múltiples métodos
- Propinas
- Apertura/cierre de caja
- Arqueo de caja

### 7. Módulo de Inventario
- Stock de ingredientes
- Recetas (ingredientes por producto)
- Descuento automático de inventario
- Alertas de stock bajo
- Compras y proveedores

### 8. Módulo de Reportes
- Ventas por período
- Productos más vendidos
- Rendimiento por mesero
- Horas pico
- Exportación PDF/Excel

### 9. Módulo de Configuración
- Datos del restaurante
- Impuestos
- Impresoras (tickets)
- Zonas de servicio
- Horarios

---

## Estructura de Vistas (Blade)

```
resources/views/
├── layouts/
│   ├── app.blade.php            # Layout principal admin
│   ├── pos.blade.php            # Layout POS (pantalla completa)
│   └── kitchen.blade.php        # Layout cocina
│
├── components/                   # Componentes Blade reutilizables
│   ├── button.blade.php
│   ├── modal.blade.php
│   ├── alert.blade.php
│   ├── card.blade.php
│   └── form/
│       ├── input.blade.php
│       ├── select.blade.php
│       └── textarea.blade.php
│
├── partials/                     # Partials globales
│   ├── header.blade.php
│   ├── sidebar.blade.php
│   ├── footer.blade.php
│   └── notifications.blade.php
│
├── admin/
│   ├── dashboard/
│   │   ├── index.blade.php
│   │   └── partials/
│   │       ├── _stats-cards.blade.php
│   │       ├── _sales-chart.blade.php
│   │       └── _recent-orders.blade.php
│   │
│   ├── products/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── partials/
│   │       ├── _form.blade.php
│   │       ├── _table.blade.php
│   │       ├── _filters.blade.php
│   │       └── _modifiers-section.blade.php
│   │
│   ├── categories/
│   │   ├── index.blade.php
│   │   └── partials/
│   │       ├── _form.blade.php
│   │       └── _tree.blade.php
│   │
│   ├── tables/
│   │   ├── index.blade.php
│   │   └── partials/
│   │       ├── _floor-map.blade.php
│   │       ├── _table-card.blade.php
│   │       └── _form.blade.php
│   │
│   ├── users/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── partials/
│   │       ├── _form.blade.php
│   │       ├── _table.blade.php
│   │       └── _permissions.blade.php
│   │
│   ├── inventory/
│   │   ├── index.blade.php
│   │   ├── movements.blade.php
│   │   └── partials/
│   │       ├── _stock-table.blade.php
│   │       ├── _movement-form.blade.php
│   │       └── _alerts.blade.php
│   │
│   ├── reports/
│   │   ├── sales.blade.php
│   │   ├── products.blade.php
│   │   ├── employees.blade.php
│   │   └── partials/
│   │       ├── _date-filter.blade.php
│   │       ├── _charts.blade.php
│   │       └── _export-buttons.blade.php
│   │
│   └── settings/
│       ├── general.blade.php
│       ├── taxes.blade.php
│       ├── printers.blade.php
│       └── partials/
│           ├── _restaurant-info.blade.php
│           ├── _tax-form.blade.php
│           └── _printer-config.blade.php
│
├── pos/
│   ├── index.blade.php           # Pantalla principal POS
│   └── partials/
│       ├── _header.blade.php
│       ├── _categories-bar.blade.php
│       ├── _products-grid.blade.php
│       ├── _product-card.blade.php
│       ├── _cart.blade.php
│       ├── _cart-item.blade.php
│       ├── _tables-modal.blade.php
│       ├── _modifiers-modal.blade.php
│       ├── _payment-modal.blade.php
│       ├── _split-bill-modal.blade.php
│       ├── _numpad.blade.php
│       └── _quick-actions.blade.php
│
├── kitchen/
│   ├── index.blade.php           # Pantalla KDS
│   └── partials/
│       ├── _order-card.blade.php
│       ├── _order-items.blade.php
│       ├── _timer.blade.php
│       └── _filters.blade.php
│
└── livewire/                     # Componentes Livewire
    ├── pos/
    │   ├── product-grid.blade.php
    │   ├── cart.blade.php
    │   └── payment-processor.blade.php
    ├── kitchen/
    │   └── order-board.blade.php
    └── admin/
        ├── sales-chart.blade.php
        └── real-time-orders.blade.php
```

---

## Modelos y Relaciones

### Modelos Principales

| Modelo | Descripción |
|--------|-------------|
| User | Usuarios del sistema |
| Category | Categorías de productos |
| Product | Productos/Platillos |
| Modifier | Modificadores de productos |
| Table | Mesas del restaurante |
| Order | Órdenes/Pedidos |
| OrderItem | Items de cada orden |
| Payment | Pagos realizados |
| CashRegister | Cajas registradoras |
| CashSession | Sesiones de caja (apertura/cierre) |
| Ingredient | Ingredientes de inventario |
| Recipe | Recetas (producto-ingrediente) |
| StockMovement | Movimientos de inventario |

### Relaciones Clave

- `User` hasMany `Order` (mesero)
- `Category` hasMany `Product`
- `Product` belongsToMany `Modifier`
- `Product` belongsToMany `Ingredient` (through Recipe)
- `Table` hasMany `Order`
- `Order` hasMany `OrderItem`
- `Order` hasMany `Payment`
- `OrderItem` belongsTo `Product`
- `OrderItem` belongsToMany `Modifier`
- `CashRegister` hasMany `CashSession`
- `CashSession` hasMany `Payment`

---

## Rutas por Módulo

```
routes/
├── web.php              # Rutas generales y auth
├── admin.php            # Rutas admin (prefix: /admin)
├── pos.php              # Rutas POS (prefix: /pos)
├── kitchen.php          # Rutas cocina (prefix: /kitchen)
└── api.php              # API REST
```

### Agrupación de Rutas

Cada archivo de rutas debe usar:
- Middleware de autenticación
- Middleware de roles/permisos
- Prefijos consistentes
- Nombres de rutas con notación punto (admin.products.index)

---

## Servicios (Lógica de Negocio)

Los controladores deben ser delgados. Toda la lógica compleja va en Services:

| Service | Responsabilidad |
|---------|-----------------|
| OrderService | Crear, modificar, cancelar órdenes |
| PaymentService | Procesar pagos, calcular cambio |
| InventoryService | Gestionar stock, alertas |
| CashService | Apertura/cierre de caja, arqueo |
| ReportService | Generar reportes y estadísticas |
| PrinterService | Imprimir tickets y comandas |
| TableService | Gestionar estados de mesas |

---

## Componentes Livewire

Para interactividad en tiempo real sin recargar página:

| Componente | Uso |
|------------|-----|
| `POS/ProductGrid` | Grid de productos con búsqueda |
| `POS/Cart` | Carrito de compras reactivo |
| `POS/PaymentProcessor` | Modal de pago |
| `Kitchen/OrderBoard` | Tablero de pedidos en tiempo real |
| `Admin/SalesChart` | Gráficos de ventas |
| `Admin/RealTimeOrders` | Monitor de órdenes en vivo |
| `Tables/FloorMap` | Mapa de mesas interactivo |

---

## Base de Datos

### Migraciones Ordenadas

1. `create_users_table`
2. `create_categories_table`
3. `create_products_table`
4. `create_modifiers_table`
5. `create_product_modifier_table` (pivot)
6. `create_tables_table`
7. `create_cash_registers_table`
8. `create_cash_sessions_table`
9. `create_orders_table`
10. `create_order_items_table`
11. `create_order_item_modifier_table` (pivot)
12. `create_payments_table`
13. `create_ingredients_table`
14. `create_recipes_table` (pivot producto-ingrediente)
15. `create_stock_movements_table`
16. `create_settings_table`

### Seeders

- `RoleSeeder` - Roles y permisos base
- `AdminUserSeeder` - Usuario admin inicial
- `CategorySeeder` - Categorías de ejemplo
- `TableSeeder` - Mesas de ejemplo
- `SettingsSeeder` - Configuración inicial

---

## Convenciones de Código

### Nombrado

| Tipo | Convención | Ejemplo |
|------|------------|---------|
| Controladores | PascalCase + Controller | `ProductController` |
| Modelos | PascalCase singular | `OrderItem` |
| Migraciones | snake_case plural | `create_order_items_table` |
| Vistas | kebab-case | `order-details.blade.php` |
| Partials | _prefijo | `_form.blade.php` |
| Rutas | dot notation | `admin.products.store` |
| Servicios | PascalCase + Service | `PaymentService` |

### Estructura de Controladores

Cada controlador debe:
- Manejar solo un recurso
- Usar Form Requests para validación
- Inyectar Services para lógica
- Retornar vistas o JSON (API)
- Máximo 7 métodos (CRUD + index)

### Estructura de Partials

- Un partial por sección lógica de la vista
- Prefijo `_` para identificarlos
- Recibir datos vía `@include('partial', ['data' => $data])`
- No más de 100 líneas por partial

---

## Paquetes Recomendados

| Paquete | Uso |
|---------|-----|
| `spatie/laravel-permission` | Roles y permisos |
| `barryvdh/laravel-dompdf` | Generación de PDFs |
| `maatwebsite/excel` | Exportación Excel |
| `livewire/livewire` | Componentes reactivos |
| `intervention/image` | Manejo de imágenes |
| `mike42/escpos-php` | Impresión de tickets |

---

## Variables de Entorno Requeridas

```
APP_NAME="POS Restaurante"
APP_ENV=production
APP_DEBUG=false

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_restaurante
DB_USERNAME=
DB_PASSWORD=

CASH_DRAWER_ENABLED=true
PRINTER_KITCHEN=
PRINTER_RECEIPT=
TAX_PERCENTAGE=16
CURRENCY=MXN
TIMEZONE=America/Mexico_City
```

---

## Flujos Principales

### Flujo de Orden

1. Mesero selecciona mesa o "Para llevar"
2. Agrega productos al carrito (con modificadores si aplica)
3. Envía orden a cocina
4. Cocina recibe y prepara (KDS)
5. Cocina marca items listos
6. Mesero entrega y procesa pago
7. Sistema actualiza inventario automáticamente
8. Se genera ticket

### Flujo de Caja

1. Cajero abre sesión con monto inicial
2. Registra pagos durante el día
3. Al cerrar, hace arqueo (conteo real vs sistema)
4. Sistema genera reporte de cierre
5. Se guarda diferencia si existe

---

## Consideraciones de Seguridad

- Validar TODOS los inputs con Form Requests
- Usar Policies para autorización de recursos
- Sanitizar datos antes de mostrar (XSS)
- Usar transacciones DB para operaciones críticas
- Registrar acciones sensibles (audit log)
- Encriptar datos sensibles
- Rate limiting en API

---

## Optimización

- Eager loading para evitar N+1
- Cache de productos y categorías
- Indexes en columnas de búsqueda frecuente
- Queues para tareas pesadas (reportes, emails)
- Compresión de imágenes al subir

---

## Testing

```
tests/
├── Feature/
│   ├── Admin/
│   │   ├── ProductTest.php
│   │   └── UserTest.php
│   ├── POS/
│   │   ├── OrderTest.php
│   │   └── PaymentTest.php
│   └── Api/
│       └── ProductApiTest.php
└── Unit/
    ├── Services/
    │   ├── OrderServiceTest.php
    │   └── PaymentServiceTest.php
    └── Models/
        └── OrderTest.php
```

---

## Comandos Artisan Personalizados

| Comando | Descripción |
|---------|-------------|
| `pos:close-sessions` | Cierra sesiones de caja abiertas |
| `pos:daily-report` | Genera reporte diario |
| `pos:check-stock` | Verifica stock y envía alertas |
| `pos:backup` | Respaldo de base de datos |

---

## Deployment

1. Clonar repositorio
2. `composer install --optimize-autoloader --no-dev`
3. `npm install && npm run build`
4. Configurar `.env`
5. `php artisan key:generate`
6. `php artisan migrate --seed`
7. `php artisan storage:link`
8. `php artisan config:cache`
9. `php artisan route:cache`
10. `php artisan view:cache`
11. Configurar queue worker (Supervisor)
12. Configurar cron para scheduler

---

## Contacto y Soporte

Documentación técnica para desarrollo del sistema POS Restaurante.
Versión del documento: 1.0
