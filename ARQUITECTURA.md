# POS Restaurante Colombia - Arquitectura Completa del Sistema

## Requisitos Técnicos

| Componente | Versión | Justificación |
|------------|---------|---------------|
| PHP | 8.3+ | Typed properties, Enums, Attributes |
| Laravel | 11.x | Compatible con PHP 8.3, características modernas |
| MySQL | 8.0+ | JSON columns, CTEs, Window functions |
| Redis | 7.x | Cache, sessions, queues, real-time |
| Node.js | 20.x LTS | Build assets, websockets |
| Tailwind CSS | 3.x | UI moderna y responsive |
| Livewire | 3.x | Interactividad sin SPA |
| Alpine.js | 3.x | Interacciones ligeras |
| Laravel Echo | + Pusher/Soketi | Tiempo real |

---

## Estructura de Directorios Completa

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── ProductController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── ModifierController.php
│   │   │   ├── ComboController.php
│   │   │   ├── TableController.php
│   │   │   ├── ZoneController.php
│   │   │   ├── UserController.php
│   │   │   ├── RoleController.php
│   │   │   ├── InventoryController.php
│   │   │   ├── SupplierController.php
│   │   │   ├── PurchaseController.php
│   │   │   ├── CustomerController.php
│   │   │   ├── ReservationController.php
│   │   │   ├── PromotionController.php
│   │   │   ├── ReportController.php
│   │   │   ├── CashRegisterController.php
│   │   │   ├── InvoiceController.php
│   │   │   ├── CreditNoteController.php
│   │   │   ├── DebitNoteController.php
│   │   │   ├── SettingsController.php
│   │   │   ├── BranchController.php
│   │   │   ├── PrinterController.php
│   │   │   ├── TaxController.php
│   │   │   ├── ResolutionController.php
│   │   │   ├── AuditController.php
│   │   │   └── BackupController.php
│   │   │
│   │   ├── POS/
│   │   │   ├── POSController.php
│   │   │   ├── OrderController.php
│   │   │   ├── PaymentController.php
│   │   │   ├── TableMapController.php
│   │   │   ├── QuickSaleController.php
│   │   │   └── DeliveryController.php
│   │   │
│   │   ├── Kitchen/
│   │   │   ├── KitchenDisplayController.php
│   │   │   ├── BarDisplayController.php
│   │   │   └── PrepStationController.php
│   │   │
│   │   ├── Waiter/
│   │   │   ├── WaiterController.php
│   │   │   └── TableServiceController.php
│   │   │
│   │   ├── Customer/
│   │   │   ├── MenuController.php
│   │   │   ├── SelfOrderController.php
│   │   │   └── FeedbackController.php
│   │   │
│   │   ├── Api/
│   │   │   └── V1/
│   │   │       ├── ProductApiController.php
│   │   │       ├── OrderApiController.php
│   │   │       ├── ReportApiController.php
│   │   │       └── WebhookController.php
│   │   │
│   │   └── Webhooks/
│   │       ├── DIANWebhookController.php
│   │       ├── PaymentGatewayController.php
│   │       └── DeliveryPlatformController.php
│   │
│   ├── Middleware/
│   │   ├── CheckBranchAccess.php
│   │   ├── CheckCashSessionOpen.php
│   │   ├── CheckSubscription.php
│   │   ├── LogActivity.php
│   │   └── CheckDIANStatus.php
│   │
│   ├── Requests/
│   │   ├── Product/
│   │   │   ├── StoreProductRequest.php
│   │   │   └── UpdateProductRequest.php
│   │   ├── Order/
│   │   │   ├── StoreOrderRequest.php
│   │   │   ├── UpdateOrderRequest.php
│   │   │   └── ProcessPaymentRequest.php
│   │   ├── Invoice/
│   │   │   ├── StoreInvoiceRequest.php
│   │   │   └── StoreCreditNoteRequest.php
│   │   ├── Customer/
│   │   │   └── StoreCustomerRequest.php
│   │   └── ... (por cada módulo)
│   │
│   └── Resources/
│       ├── ProductResource.php
│       ├── ProductCollection.php
│       ├── OrderResource.php
│       ├── InvoiceResource.php
│       └── ...
│
├── Models/
│   ├── User.php
│   ├── Branch.php
│   ├── Category.php
│   ├── Product.php
│   ├── ProductVariant.php
│   ├── Modifier.php
│   ├── ModifierGroup.php
│   ├── Combo.php
│   ├── ComboItem.php
│   ├── Promotion.php
│   ├── PromotionRule.php
│   ├── Zone.php
│   ├── Table.php
│   ├── Reservation.php
│   ├── Customer.php
│   ├── CustomerAddress.php
│   ├── LoyaltyPoint.php
│   ├── Order.php
│   ├── OrderItem.php
│   ├── OrderItemModifier.php
│   ├── OrderDiscount.php
│   ├── OrderTax.php
│   ├── Payment.php
│   ├── PaymentMethod.php
│   ├── Tip.php
│   ├── CashRegister.php
│   ├── CashSession.php
│   ├── CashMovement.php
│   ├── Invoice.php
│   ├── InvoiceLine.php
│   ├── CreditNote.php
│   ├── DebitNote.php
│   ├── DIANResolution.php
│   ├── DIANDocument.php
│   ├── Ingredient.php
│   ├── Recipe.php
│   ├── RecipeItem.php
│   ├── StockMovement.php
│   ├── Supplier.php
│   ├── PurchaseOrder.php
│   ├── PurchaseOrderItem.php
│   ├── Printer.php
│   ├── PrintJob.php
│   ├── Tax.php
│   ├── Setting.php
│   ├── AuditLog.php
│   └── Notification.php
│
├── Services/
│   ├── Order/
│   │   ├── OrderService.php
│   │   ├── OrderCalculationService.php
│   │   ├── OrderStatusService.php
│   │   └── SplitBillService.php
│   │
│   ├── Payment/
│   │   ├── PaymentService.php
│   │   ├── PaymentGatewayService.php
│   │   ├── CashService.php
│   │   └── TipService.php
│   │
│   ├── Invoice/
│   │   ├── InvoiceService.php
│   │   ├── InvoiceCalculationService.php
│   │   ├── CreditNoteService.php
│   │   └── DebitNoteService.php
│   │
│   ├── DIAN/
│   │   ├── DIANService.php
│   │   ├── DIANAuthService.php
│   │   ├── DIANDocumentBuilder.php
│   │   ├── DIANXMLGenerator.php
│   │   ├── DIANSignatureService.php
│   │   ├── DIANCUFEGenerator.php
│   │   ├── DIANQRGenerator.php
│   │   ├── DIANValidationService.php
│   │   └── DIANSyncService.php
│   │
│   ├── Inventory/
│   │   ├── InventoryService.php
│   │   ├── StockAlertService.php
│   │   ├── RecipeService.php
│   │   └── StockMovementService.php
│   │
│   ├── Kitchen/
│   │   ├── KitchenService.php
│   │   ├── KitchenDisplayService.php
│   │   └── PrepTimeService.php
│   │
│   ├── Table/
│   │   ├── TableService.php
│   │   ├── ReservationService.php
│   │   └── WaitlistService.php
│   │
│   ├── Customer/
│   │   ├── CustomerService.php
│   │   ├── LoyaltyService.php
│   │   └── CustomerNotificationService.php
│   │
│   ├── Report/
│   │   ├── ReportService.php
│   │   ├── SalesReportService.php
│   │   ├── InventoryReportService.php
│   │   ├── TaxReportService.php
│   │   ├── EmployeeReportService.php
│   │   └── ExportService.php
│   │
│   ├── Printer/
│   │   ├── PrinterService.php
│   │   ├── ReceiptPrinterService.php
│   │   ├── KitchenPrinterService.php
│   │   └── LabelPrinterService.php
│   │
│   ├── Delivery/
│   │   ├── DeliveryService.php
│   │   ├── RappiIntegrationService.php
│   │   ├── IFoodIntegrationService.php
│   │   └── UberEatsIntegrationService.php
│   │
│   ├── Notification/
│   │   ├── NotificationService.php
│   │   ├── WhatsAppService.php
│   │   ├── SMSService.php
│   │   └── EmailService.php
│   │
│   └── Sync/
│       ├── OfflineSyncService.php
│       └── BranchSyncService.php
│
├── Repositories/
│   ├── Contracts/
│   │   ├── ProductRepositoryInterface.php
│   │   ├── OrderRepositoryInterface.php
│   │   ├── CustomerRepositoryInterface.php
│   │   └── ...
│   └── Eloquent/
│       ├── ProductRepository.php
│       ├── OrderRepository.php
│       ├── CustomerRepository.php
│       └── ...
│
├── Events/
│   ├── Order/
│   │   ├── OrderCreated.php
│   │   ├── OrderUpdated.php
│   │   ├── OrderCompleted.php
│   │   ├── OrderCancelled.php
│   │   └── OrderItemReady.php
│   ├── Payment/
│   │   ├── PaymentReceived.php
│   │   └── PaymentFailed.php
│   ├── Invoice/
│   │   ├── InvoiceGenerated.php
│   │   ├── InvoiceSentToDIAN.php
│   │   └── InvoiceApprovedByDIAN.php
│   ├── Kitchen/
│   │   ├── NewOrderInKitchen.php
│   │   └── OrderItemPrepared.php
│   ├── Inventory/
│   │   ├── LowStockAlert.php
│   │   └── StockUpdated.php
│   └── Table/
│       ├── TableStatusChanged.php
│       └── ReservationCreated.php
│
├── Listeners/
│   ├── Order/
│   │   ├── SendOrderToKitchen.php
│   │   ├── UpdateInventoryOnOrder.php
│   │   ├── NotifyWaiterOrderReady.php
│   │   └── UpdateCustomerLoyalty.php
│   ├── Invoice/
│   │   ├── SendInvoiceToDIAN.php
│   │   ├── SendInvoiceToCustomer.php
│   │   └── PrintInvoice.php
│   └── Inventory/
│       └── SendLowStockNotification.php
│
├── Jobs/
│   ├── DIAN/
│   │   ├── SendDocumentToDIAN.php
│   │   ├── SyncDIANStatus.php
│   │   └── RetryFailedDIANDocuments.php
│   ├── Report/
│   │   ├── GenerateDailyReport.php
│   │   ├── GenerateMonthlyReport.php
│   │   └── ExportLargeReport.php
│   ├── Invoice/
│   │   ├── GenerateInvoicePDF.php
│   │   └── SendInvoiceEmail.php
│   ├── Inventory/
│   │   └── ProcessStockMovements.php
│   ├── Notification/
│   │   ├── SendWhatsAppNotification.php
│   │   └── SendSMSNotification.php
│   └── Backup/
│       └── CreateDatabaseBackup.php
│
├── Enums/
│   ├── OrderStatus.php
│   ├── OrderType.php
│   ├── PaymentStatus.php
│   ├── PaymentMethodType.php
│   ├── TableStatus.php
│   ├── InvoiceStatus.php
│   ├── DIANDocumentType.php
│   ├── DIANDocumentStatus.php
│   ├── TaxType.php
│   ├── DiscountType.php
│   ├── StockMovementType.php
│   ├── UserRole.php
│   ├── ReservationStatus.php
│   ├── CustomerType.php
│   └── PrinterType.php
│
├── Traits/
│   ├── HasAuditLog.php
│   ├── HasBranch.php
│   ├── HasMedia.php
│   ├── Filterable.php
│   ├── Sortable.php
│   └── HasDIANDocument.php
│
├── Observers/
│   ├── OrderObserver.php
│   ├── InvoiceObserver.php
│   ├── ProductObserver.php
│   └── InventoryObserver.php
│
├── Policies/
│   ├── OrderPolicy.php
│   ├── ProductPolicy.php
│   ├── InvoicePolicy.php
│   ├── CashSessionPolicy.php
│   └── ReportPolicy.php
│
├── Rules/
│   ├── ValidNIT.php
│   ├── ValidCedulaColombia.php
│   ├── ValidDIANResolution.php
│   └── ValidPaymentAmount.php
│
├── Console/
│   └── Commands/
│       ├── DIAN/
│       │   ├── SyncDIANDocuments.php
│       │   ├── CheckDIANResolutions.php
│       │   └── RetryFailedInvoices.php
│       ├── Reports/
│       │   ├── GenerateDailyReport.php
│       │   └── SendScheduledReports.php
│       ├── Maintenance/
│       │   ├── CloseOpenCashSessions.php
│       │   ├── CleanOldAuditLogs.php
│       │   └── DatabaseBackup.php
│       └── Alerts/
│           ├── CheckLowStock.php
│           └── CheckExpiringResolutions.php
│
├── Providers/
│   ├── AppServiceProvider.php
│   ├── EventServiceProvider.php
│   ├── RepositoryServiceProvider.php
│   ├── DIANServiceProvider.php
│   └── ViewComposerServiceProvider.php
│
└── Helpers/
    ├── MoneyHelper.php
    ├── TaxHelper.php
    ├── DateHelper.php
    └── DIANHelper.php
```

---

## Módulos del Sistema

### 1. Módulo de Autenticación y Usuarios

**Funcionalidades:**
- Login con email/password
- Login rápido con PIN (4-6 dígitos) para POS
- Autenticación por huella digital (opcional)
- Gestión de roles: SuperAdmin, Admin, Gerente, Cajero, Mesero, Cocina, Domiciliario, Contador
- Permisos granulares por módulo y acción
- Multi-sucursal con acceso controlado
- Sesiones activas y cierre remoto
- Bloqueo automático por inactividad
- Recuperación de contraseña
- Log de accesos

**Permisos Clave:**
- `pos.access` - Acceso al punto de venta
- `orders.create`, `orders.edit`, `orders.cancel`, `orders.discount`
- `payments.process`, `payments.refund`
- `cash.open`, `cash.close`, `cash.movements`
- `invoices.create`, `invoices.void`
- `reports.view`, `reports.export`
- `inventory.view`, `inventory.adjust`
- `settings.manage`

---

### 2. Módulo de Productos y Menú

**Funcionalidades:**
- CRUD completo de productos
- Categorías multinivel (ej: Bebidas > Calientes > Café)
- Subcategorías ilimitadas
- Variantes de producto (tamaño, presentación)
- Grupos de modificadores (ej: "Tipo de leche", "Término de carne")
- Modificadores con precio adicional
- Combos y menús del día
- Productos compuestos (recetas)
- Control de disponibilidad por horario
- Disponibilidad por sucursal
- Productos destacados/populares
- Imágenes múltiples por producto
- Códigos de barras/SKU
- Ordenamiento personalizado
- Impuestos por producto (IVA, Impoconsumo)
- Precios por sucursal
- Histórico de precios

**Consideraciones:**
- Producto puede tener múltiples impuestos
- Impoconsumo (8%) para bebidas azucaradas y comidas ultra procesadas
- IVA (19%) según categoría
- Productos exentos y excluidos de IVA

---

### 3. Módulo de Mesas y Zonas

**Funcionalidades:**
- Zonas del restaurante (Salón, Terraza, Barra, VIP)
- Mapa visual drag-and-drop del restaurante
- Estados de mesa: Libre, Ocupada, Reservada, Por limpiar, Cuenta pedida
- Capacidad por mesa
- Unir mesas temporalmente
- Dividir mesa (cuentas separadas)
- Transferir mesa entre meseros
- Transferir pedido entre mesas
- Asignación automática de mesero por zona
- Timer de ocupación
- Alerta de mesa sin atender
- Rotación de mesas para equidad

---

### 4. Módulo de Reservaciones

**Funcionalidades:**
- Crear reservaciones por teléfono/web/app
- Calendario visual de reservaciones
- Bloqueo automático de mesas reservadas
- Confirmación por WhatsApp/SMS/Email
- Recordatorio automático
- Lista de espera (waitlist)
- No-show tracking
- Historial de reservaciones por cliente
- Notas especiales (cumpleaños, preferencias)
- Depósito para reservaciones grandes
- Cancelación con políticas

---

### 5. Módulo de Clientes

**Funcionalidades:**
- Registro de clientes (opcional para facturación)
- Tipos: Persona Natural, Persona Jurídica
- Datos fiscales: NIT, Cédula, Razón Social, Régimen
- Direcciones múltiples (para domicilios)
- Teléfonos y emails
- Historial de compras
- Preferencias y alergias
- Notas internas
- Programa de fidelización (puntos)
- Acumulación y redención de puntos
- Niveles de cliente (Bronce, Plata, Oro)
- Crédito a clientes corporativos
- Estado de cuenta
- Comunicaciones (cumpleaños, promociones)

---

### 6. Módulo de Órdenes/Pedidos

**Funcionalidades:**
- Crear orden para mesa o para llevar
- Orden para domicilio
- Orden desde plataformas (Rappi, iFood)
- Agregar/quitar items
- Modificadores por item
- Notas especiales por item ("sin cebolla", "extra picante")
- Cantidad fraccionada (por peso)
- División de cuenta (por item, por porcentaje, por persona, igualitario)
- Aplicar descuentos (porcentaje, valor fijo)
- Descuentos con autorización de supervisor
- Cortesías con motivo obligatorio
- Cambio de precios con autorización
- Envío parcial a cocina
- Cancelación de items (con motivo)
- Anulación de orden (con autorización)
- Estados de orden: Pendiente, En preparación, Listo, Entregado, Pagado, Anulado
- Estados por item: Pendiente, Preparando, Listo, Entregado
- Tiempo estimado de entrega
- Priorización de órdenes
- Historial completo de cambios (quién, qué, cuándo)
- Reimprimir comanda
- Pre-cuenta para cliente

---

### 7. Módulo de Cocina (KDS - Kitchen Display System)

**Funcionalidades:**
- Pantalla de pedidos en tiempo real
- Filtro por estación (Cocina caliente, Cocina fría, Parrilla, Bar)
- Vista de items pendientes
- Marcar items como "Preparando"
- Marcar items como "Listo"
- Timer por pedido (alerta de demora)
- Código de colores por tiempo
- Bump bar / Touch screen
- Sonido de alerta nuevo pedido
- Recall de pedidos completados
- Estadísticas de tiempos de preparación
- Modo "Rush" para horas pico
- Agrupación de items iguales
- Notas especiales destacadas
- Vista de "All Day" (totales del día)

---

### 8. Módulo de Pagos

**Funcionalidades:**
- Métodos de pago configurables:
  - Efectivo (con cálculo de cambio)
  - Tarjeta débito
  - Tarjeta crédito
  - Transferencia/Nequi/Daviplata
  - Bonos/Vales
  - Puntos de fidelidad
  - Crédito (cuenta por cobrar)
  - Mixto (múltiples métodos)
- Propinas (sugeridas y personalizadas)
- Propinas por porcentaje del servicio
- División de pago entre comensales
- Facturación electrónica automática
- Documento soporte para no obligados
- Impresión de recibo
- Envío de factura por email/WhatsApp
- Anulación de pagos (con autorización)
- Devoluciones parciales
- Pagos parciales (abonos)

---

### 9. Módulo de Caja

**Funcionalidades:**
- Apertura de caja con monto base
- Múltiples cajas por sucursal
- Asignación de cajero a caja
- Movimientos de caja:
  - Ingresos (pagos)
  - Egresos (gastos menores)
  - Retiros parciales
  - Préstamos entre cajas
- Arqueo de caja (conteo por denominación)
- Diferencias (faltantes/sobrantes)
- Cierre de caja con reporte
- Corte X (parcial sin cerrar)
- Corte Z (cierre definitivo)
- Histórico de cierres
- Entrega a tesorería
- Consecutivo de recibos de caja

---

### 10. Módulo de Facturación Electrónica DIAN

**Tipos de Documentos:**
- Factura Electrónica de Venta
- Nota Crédito Electrónica
- Nota Débito Electrónica
- Documento Soporte (para no obligados a facturar)
- POS electrónico (documento equivalente)

**Funcionalidades:**
- Resolución de facturación DIAN
- Control de consecutivos por resolución
- Alerta de vencimiento de resolución
- Alerta de agotamiento de consecutivos
- Generación de XML según estándar UBL 2.1
- Firma digital con certificado
- Generación de CUFE/CUDE
- Código QR con información fiscal
- Envío automático a DIAN
- Recepción de respuesta (ApplicationResponse)
- Reintentos automáticos en fallo
- Cola de documentos pendientes
- Log de comunicación con DIAN
- Representación gráfica (PDF)
- Envío al cliente (email)
- Consulta de facturas emitidas
- Anulación (solo con Nota Crédito)
- Contingencia (facturación offline)
- Sincronización post-contingencia
- Reporte de facturas para contabilidad
- Integración con software contable

**Datos Requeridos por DIAN:**
- NIT del emisor con DV
- Razón social
- Dirección fiscal
- Régimen tributario
- Responsabilidades fiscales
- Actividad económica CIIU
- Datos del adquirente (NIT/CC, nombre, dirección)
- Forma de pago (Contado/Crédito)
- Método de pago
- Fechas de vencimiento
- Impuestos discriminados (IVA, INC, otros)
- Retenciones si aplican

---

### 11. Módulo de Notas Crédito y Débito

**Nota Crédito:**
- Anulación total de factura
- Devolución parcial de productos
- Descuento posterior
- Ajuste de precios
- Referencia obligatoria a factura original
- Envío a DIAN

**Nota Débito:**
- Intereses por mora
- Ajuste de valores
- Cobros adicionales
- Referencia a factura original
- Envío a DIAN

---

### 12. Módulo de Inventario

**Funcionalidades:**
- Ingredientes y materias primas
- Productos terminados
- Unidades de medida (kg, lt, und, etc.)
- Conversión de unidades
- Stock mínimo y máximo
- Múltiples bodegas/almacenes
- Recetas (lista de ingredientes por producto)
- Descuento automático de inventario al vender
- Costeo de recetas
- Mermas y desperdicios
- Toma física de inventario
- Ajustes de inventario (con motivo)
- Kardex (movimientos por producto)
- Alertas de stock bajo
- Productos por agotarse
- Productos sin movimiento
- Lotes y fechas de vencimiento
- Trazabilidad

**Tipos de Movimiento:**
- Entrada por compra
- Entrada por devolución
- Entrada por ajuste
- Entrada por transferencia
- Salida por venta
- Salida por consumo interno
- Salida por merma/desperdicio
- Salida por ajuste
- Salida por transferencia

---

### 13. Módulo de Compras y Proveedores

**Funcionalidades:**
- Registro de proveedores
- Datos fiscales del proveedor
- Contactos por proveedor
- Catálogo de productos por proveedor
- Órdenes de compra
- Recepción de mercancía
- Recepción parcial
- Factura de proveedor
- Cuentas por pagar
- Histórico de precios de compra
- Evaluación de proveedores
- Reposición automática sugerida

---

### 14. Módulo de Promociones y Descuentos

**Tipos de Promociones:**
- Descuento por porcentaje
- Descuento por valor fijo
- 2x1, 3x2, etc.
- Combo a precio especial
- Happy Hour (por horario)
- Día especial (Martes de tacos)
- Por método de pago
- Por monto mínimo
- Primera compra
- Cliente frecuente
- Código promocional
- Cupones

**Reglas:**
- Vigencia (fecha inicio/fin)
- Horarios de aplicación
- Días de la semana
- Sucursales aplicables
- Productos incluidos/excluidos
- Categorías incluidas/excluidas
- Límite de usos totales
- Límite de usos por cliente
- Acumulable con otras promociones

---

### 15. Módulo de Domicilios

**Funcionalidades:**
- Orden para domicilio
- Zonas de cobertura
- Costo de envío por zona
- Tiempo estimado por zona
- Envío gratis por monto mínimo
- Asignación de domiciliario
- Estados: Preparando, Listo, En camino, Entregado
- Tracking en tiempo real (GPS)
- Confirmación de entrega
- Cobro contraentrega
- Integración con Rappi, iFood, Uber Eats
- Sincronización de menú con plataformas
- Gestión de órdenes externas
- Reporte de comisiones por plataforma

---

### 16. Módulo de Reportes y Analytics

**Reportes de Ventas:**
- Ventas del día/semana/mes/año
- Ventas por hora (horas pico)
- Ventas por categoría
- Ventas por producto
- Productos más vendidos
- Productos menos vendidos
- Ventas por mesero
- Ventas por mesa/zona
- Ventas por método de pago
- Ventas por tipo (mesa, llevar, domicilio)
- Ticket promedio
- Comensales por mesa
- Comparativo períodos anteriores

**Reportes Fiscales:**
- Libro de ventas diario
- Resumen de impuestos (IVA, Impoconsumo)
- Facturas emitidas
- Notas crédito/débito
- Documentos rechazados por DIAN
- Reporte para declaración de IVA
- Medios magnéticos

**Reportes de Inventario:**
- Stock actual
- Valorización de inventario
- Productos por agotarse
- Movimientos de inventario
- Mermas y desperdicios
- Consumo vs ventas (teórico vs real)
- Costo de ventas

**Reportes de Caja:**
- Resumen de caja por turno
- Movimientos del día
- Propinas recaudadas
- Diferencias de caja

**Reportes de Personal:**
- Ventas por empleado
- Propinas por mesero
- Horas trabajadas
- Productividad

**Reportes de Clientes:**
- Clientes nuevos
- Frecuencia de visita
- Ticket promedio por cliente
- Top clientes
- Puntos acumulados/redimidos

**Dashboard:**
- Widgets configurables
- Métricas en tiempo real
- Gráficos interactivos
- Exportación PDF/Excel
- Envío programado por email

---

### 17. Módulo de Configuración

**General:**
- Datos del restaurante (nombre, logo, NIT)
- Información de contacto
- Redes sociales
- Moneda y formato de números
- Zona horaria
- Idioma
- Términos y condiciones (para tickets)

**Sucursales:**
- Múltiples sucursales
- Datos por sucursal
- Resolución DIAN por sucursal
- Horarios por sucursal
- Impresoras por sucursal

**Impuestos:**
- Configuración de IVA
- Configuración de Impoconsumo
- Productos exentos
- Productos excluidos
- Retenciones

**Resoluciones DIAN:**
- Número de resolución
- Fecha de resolución
- Rango de numeración (desde - hasta)
- Fecha de vigencia
- Prefijo
- Clave técnica
- Ambiente (Pruebas/Producción)

**Impresoras:**
- Impresoras de tickets
- Impresoras de cocina
- Impresoras de etiquetas
- Asignación por estación
- Plantillas de impresión

**Integraciones:**
- API Keys de plataformas de delivery
- Configuración de pasarelas de pago
- Webhooks
- WhatsApp Business API
- Certificado digital DIAN

**Notificaciones:**
- Alertas por email
- Alertas por WhatsApp
- Eventos a notificar
- Destinatarios por tipo

---

### 18. Módulo de Auditoría

**Funcionalidades:**
- Log de todas las acciones
- Quién hizo qué y cuándo
- Cambios en registros (antes/después)
- Filtros por usuario, fecha, módulo, acción
- Accesos al sistema
- Intentos fallidos de login
- Acciones sensibles destacadas
- Exportación de logs
- Retención configurable

---

### 19. Módulo de Turnos y Personal

**Funcionalidades:**
- Registro de empleados
- Cargos y salarios
- Turnos y horarios
- Entrada/salida (reloj)
- Asistencia
- Horas extras
- Asignación de zonas/mesas
- Metas de venta por empleado
- Comisiones

---

### 20. Módulo Offline / Contingencia

**Funcionalidades:**
- Detección de pérdida de conexión
- Modo offline automático
- Almacenamiento local de órdenes
- Almacenamiento local de pagos
- Numeración de contingencia
- Sincronización al recuperar conexión
- Cola de documentos pendientes DIAN
- Conflicto de datos (resolución)
- Indicador visual de modo offline

---

## Estructura de Vistas (Blade)

```
resources/views/
├── layouts/
│   ├── app.blade.php                    # Admin layout
│   ├── pos.blade.php                    # POS layout (fullscreen)
│   ├── kitchen.blade.php                # Kitchen display
│   ├── waiter.blade.php                 # Waiter mobile
│   ├── customer.blade.php               # Customer facing
│   ├── auth.blade.php                   # Login pages
│   └── print.blade.php                  # Print templates
│
├── components/
│   ├── ui/
│   │   ├── button.blade.php
│   │   ├── modal.blade.php
│   │   ├── dropdown.blade.php
│   │   ├── card.blade.php
│   │   ├── badge.blade.php
│   │   ├── alert.blade.php
│   │   ├── tabs.blade.php
│   │   ├── table.blade.php
│   │   ├── pagination.blade.php
│   │   └── loading.blade.php
│   │
│   ├── form/
│   │   ├── input.blade.php
│   │   ├── textarea.blade.php
│   │   ├── select.blade.php
│   │   ├── checkbox.blade.php
│   │   ├── radio.blade.php
│   │   ├── switch.blade.php
│   │   ├── file-upload.blade.php
│   │   ├── date-picker.blade.php
│   │   ├── time-picker.blade.php
│   │   ├── money-input.blade.php
│   │   └── search.blade.php
│   │
│   ├── pos/
│   │   ├── product-card.blade.php
│   │   ├── cart-item.blade.php
│   │   ├── numpad.blade.php
│   │   ├── payment-method.blade.php
│   │   └── table-badge.blade.php
│   │
│   └── reports/
│       ├── stat-card.blade.php
│       ├── chart.blade.php
│       └── data-table.blade.php
│
├── partials/
│   ├── header.blade.php
│   ├── sidebar.blade.php
│   ├── footer.blade.php
│   ├── breadcrumbs.blade.php
│   ├── notifications.blade.php
│   ├── flash-messages.blade.php
│   └── offline-indicator.blade.php
│
├── auth/
│   ├── login.blade.php
│   ├── pin-login.blade.php
│   ├── forgot-password.blade.php
│   ├── reset-password.blade.php
│   └── partials/
│       ├── _login-form.blade.php
│       └── _pin-pad.blade.php
│
├── admin/
│   ├── dashboard/
│   │   ├── index.blade.php
│   │   └── partials/
│   │       ├── _stats-row.blade.php
│   │       ├── _sales-chart.blade.php
│   │       ├── _top-products.blade.php
│   │       ├── _recent-orders.blade.php
│   │       ├── _alerts.blade.php
│   │       └── _quick-actions.blade.php
│   │
│   ├── products/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   ├── show.blade.php
│   │   └── partials/
│   │       ├── _filters.blade.php
│   │       ├── _table.blade.php
│   │       ├── _form.blade.php
│   │       ├── _basic-info.blade.php
│   │       ├── _pricing.blade.php
│   │       ├── _inventory.blade.php
│   │       ├── _modifiers.blade.php
│   │       ├── _images.blade.php
│   │       └── _availability.blade.php
│   │
│   ├── categories/
│   │   ├── index.blade.php
│   │   └── partials/
│   │       ├── _tree.blade.php
│   │       ├── _form.blade.php
│   │       └── _item.blade.php
│   │
│   ├── modifiers/
│   │   ├── index.blade.php
│   │   └── partials/
│   │       ├── _groups-list.blade.php
│   │       ├── _group-form.blade.php
│   │       └── _modifier-form.blade.php
│   │
│   ├── combos/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── partials/
│   │       ├── _form.blade.php
│   │       └── _items-selector.blade.php
│   │
│   ├── tables/
│   │   ├── index.blade.php
│   │   └── partials/
│   │       ├── _floor-map.blade.php
│   │       ├── _zones-list.blade.php
│   │       ├── _table-form.blade.php
│   │       └── _zone-form.blade.php
│   │
│   ├── reservations/
│   │   ├── index.blade.php
│   │   ├── calendar.blade.php
│   │   └── partials/
│   │       ├── _calendar-view.blade.php
│   │       ├── _list-view.blade.php
│   │       ├── _form.blade.php
│   │       └── _waitlist.blade.php
│   │
│   ├── customers/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   ├── show.blade.php
│   │   └── partials/
│   │       ├── _filters.blade.php
│   │       ├── _table.blade.php
│   │       ├── _form.blade.php
│   │       ├── _fiscal-data.blade.php
│   │       ├── _addresses.blade.php
│   │       ├── _purchase-history.blade.php
│   │       ├── _loyalty-info.blade.php
│   │       └── _credit-account.blade.php
│   │
│   ├── orders/
│   │   ├── index.blade.php
│   │   ├── show.blade.php
│   │   └── partials/
│   │       ├── _filters.blade.php
│   │       ├── _table.blade.php
│   │       ├── _order-details.blade.php
│   │       ├── _order-items.blade.php
│   │       ├── _payments.blade.php
│   │       ├── _timeline.blade.php
│   │       └── _actions.blade.php
│   │
│   ├── invoices/
│   │   ├── index.blade.php
│   │   ├── show.blade.php
│   │   └── partials/
│   │       ├── _filters.blade.php
│   │       ├── _table.blade.php
│   │       ├── _invoice-details.blade.php
│   │       ├── _dian-status.blade.php
│   │       └── _actions.blade.php
│   │
│   ├── credit-notes/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── partials/
│   │       ├── _form.blade.php
│   │       ├── _invoice-selector.blade.php
│   │       └── _items.blade.php
│   │
│   ├── cash-registers/
│   │   ├── index.blade.php
│   │   ├── sessions.blade.php
│   │   └── partials/
│   │       ├── _registers-list.blade.php
│   │       ├── _open-session.blade.php
│   │       ├── _close-session.blade.php
│   │       ├── _cash-count.blade.php
│   │       ├── _movements.blade.php
│   │       └── _session-report.blade.php
│   │
│   ├── inventory/
│   │   ├── index.blade.php
│   │   ├── ingredients/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   └── partials/
│   │   │       ├── _form.blade.php
│   │   │       └── _stock-info.blade.php
│   │   ├── recipes/
│   │   │   ├── index.blade.php
│   │   │   └── partials/
│   │   │       ├── _recipe-form.blade.php
│   │   │       └── _ingredients-list.blade.php
│   │   ├── movements/
│   │   │   ├── index.blade.php
│   │   │   └── partials/
│   │   │       ├── _filters.blade.php
│   │   │       ├── _table.blade.php
│   │   │       └── _new-movement.blade.php
│   │   ├── stock-take/
│   │   │   └── index.blade.php
│   │   └── partials/
│   │       ├── _stock-table.blade.php
│   │       ├── _alerts.blade.php
│   │       └── _kardex.blade.php
│   │
│   ├── suppliers/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── partials/
│   │       ├── _form.blade.php
│   │       └── _products.blade.php
│   │
│   ├── purchases/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── partials/
│   │       ├── _form.blade.php
│   │       ├── _items.blade.php
│   │       └── _receive.blade.php
│   │
│   ├── promotions/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── partials/
│   │       ├── _form.blade.php
│   │       ├── _rules.blade.php
│   │       ├── _products.blade.php
│   │       └── _schedule.blade.php
│   │
│   ├── reports/
│   │   ├── index.blade.php
│   │   ├── sales.blade.php
│   │   ├── products.blade.php
│   │   ├── inventory.blade.php
│   │   ├── taxes.blade.php
│   │   ├── employees.blade.php
│   │   ├── customers.blade.php
│   │   ├── cash.blade.php
│   │   └── partials/
│   │       ├── _date-range.blade.php
│   │       ├── _filters.blade.php
│   │       ├── _export-buttons.blade.php
│   │       ├── _sales-summary.blade.php
│   │       ├── _sales-by-category.blade.php
│   │       ├── _sales-by-product.blade.php
│   │       ├── _sales-by-hour.blade.php
│   │       ├── _tax-summary.blade.php
│   │       └── _comparison.blade.php
│   │
│   ├── users/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── partials/
│   │       ├── _form.blade.php
│   │       ├── _permissions.blade.php
│   │       └── _branches.blade.php
│   │
│   ├── roles/
│   │   ├── index.blade.php
│   │   └── partials/
│   │       ├── _form.blade.php
│   │       └── _permissions-matrix.blade.php
│   │
│   ├── settings/
│   │   ├── general.blade.php
│   │   ├── branches.blade.php
│   │   ├── taxes.blade.php
│   │   ├── resolutions.blade.php
│   │   ├── printers.blade.php
│   │   ├── integrations.blade.php
│   │   ├── notifications.blade.php
│   │   └── partials/
│   │       ├── _restaurant-info.blade.php
│   │       ├── _branch-form.blade.php
│   │       ├── _tax-form.blade.php
│   │       ├── _resolution-form.blade.php
│   │       ├── _printer-form.blade.php
│   │       ├── _dian-config.blade.php
│   │       ├── _delivery-platforms.blade.php
│   │       └── _whatsapp-config.blade.php
│   │
│   └── audit/
│       ├── index.blade.php
│       └── partials/
│           ├── _filters.blade.php
│           └── _log-table.blade.php
│
├── pos/
│   ├── index.blade.php
│   └── partials/
│       ├── _header.blade.php
│       ├── _user-info.blade.php
│       ├── _categories-bar.blade.php
│       ├── _products-grid.blade.php
│       ├── _product-card.blade.php
│       ├── _search-bar.blade.php
│       ├── _cart.blade.php
│       ├── _cart-header.blade.php
│       ├── _cart-items.blade.php
│       ├── _cart-item.blade.php
│       ├── _cart-totals.blade.php
│       ├── _cart-actions.blade.php
│       ├── _numpad.blade.php
│       ├── _quick-amounts.blade.php
│       ├── _tables-modal.blade.php
│       ├── _table-map.blade.php
│       ├── _table-card.blade.php
│       ├── _customer-modal.blade.php
│       ├── _customer-search.blade.php
│       ├── _customer-form.blade.php
│       ├── _modifiers-modal.blade.php
│       ├── _modifier-group.blade.php
│       ├── _item-notes-modal.blade.php
│       ├── _discount-modal.blade.php
│       ├── _supervisor-auth-modal.blade.php
│       ├── _payment-modal.blade.php
│       ├── _payment-methods.blade.php
│       ├── _payment-cash.blade.php
│       ├── _payment-card.blade.php
│       ├── _payment-transfer.blade.php
│       ├── _payment-summary.blade.php
│       ├── _tip-selector.blade.php
│       ├── _split-bill-modal.blade.php
│       ├── _split-options.blade.php
│       ├── _orders-list-modal.blade.php
│       ├── _order-row.blade.php
│       ├── _pre-bill.blade.php
│       ├── _delivery-modal.blade.php
│       ├── _delivery-form.blade.php
│       └── _offline-banner.blade.php
│
├── kitchen/
│   ├── index.blade.php
│   └── partials/
│       ├── _header.blade.php
│       ├── _filters.blade.php
│       ├── _order-board.blade.php
│       ├── _order-card.blade.php
│       ├── _order-items.blade.php
│       ├── _order-item.blade.php
│       ├── _timer.blade.php
│       ├── _all-day-view.blade.php
│       ├── _completed-orders.blade.php
│       └── _stats.blade.php
│
├── waiter/
│   ├── index.blade.php
│   └── partials/
│       ├── _tables-view.blade.php
│       ├── _my-orders.blade.php
│       ├── _notifications.blade.php
│       └── _quick-actions.blade.php
│
├── customer/
│   ├── menu.blade.php
│   ├── self-order.blade.php
│   └── partials/
│       ├── _menu-category.blade.php
│       ├── _menu-item.blade.php
│       └── _order-summary.blade.php
│
├── print/
│   ├── receipt.blade.php
│   ├── invoice.blade.php
│   ├── kitchen-ticket.blade.php
│   ├── pre-bill.blade.php
│   ├── cash-report.blade.php
│   └── partials/
│       ├── _header.blade.php
│       ├── _items.blade.php
│       ├── _totals.blade.php
│       ├── _fiscal-info.blade.php
│       ├── _qr-code.blade.php
│       └── _footer.blade.php
│
├── emails/
│   ├── invoice.blade.php
│   ├── reservation-confirmation.blade.php
│   ├── reservation-reminder.blade.php
│   ├── daily-report.blade.php
│   └── low-stock-alert.blade.php
│
├── pdf/
│   ├── invoice.blade.php
│   ├── credit-note.blade.php
│   ├── report.blade.php
│   └── partials/
│       ├── _header.blade.php
│       ├── _fiscal-header.blade.php
│       └── _footer.blade.php
│
└── livewire/
    ├── pos/
    │   ├── main.blade.php
    │   ├── product-grid.blade.php
    │   ├── cart.blade.php
    │   ├── table-selector.blade.php
    │   ├── customer-selector.blade.php
    │   ├── payment-processor.blade.php
    │   └── order-history.blade.php
    │
    ├── kitchen/
    │   ├── order-board.blade.php
    │   └── order-card.blade.php
    │
    ├── admin/
    │   ├── dashboard-stats.blade.php
    │   ├── sales-chart.blade.php
    │   ├── real-time-orders.blade.php
    │   ├── product-table.blade.php
    │   ├── customer-table.blade.php
    │   └── notifications-dropdown.blade.php
    │
    ├── tables/
    │   ├── floor-map.blade.php
    │   └── table-status.blade.php
    │
    ├── inventory/
    │   ├── stock-table.blade.php
    │   └── alerts-panel.blade.php
    │
    └── reports/
        ├── date-range-selector.blade.php
        └── chart-container.blade.php
```

---

## Base de Datos - Migraciones

### Orden de Migraciones

```
1.  create_users_table
2.  create_branches_table
3.  create_branch_user_table (pivot)
4.  create_roles_permissions_tables (Spatie)
5.  create_categories_table
6.  create_products_table
7.  create_product_variants_table
8.  create_modifier_groups_table
9.  create_modifiers_table
10. create_product_modifier_group_table (pivot)
11. create_combos_table
12. create_combo_items_table
13. create_promotions_table
14. create_promotion_rules_table
15. create_zones_table
16. create_tables_table
17. create_reservations_table
18. create_customers_table
19. create_customer_addresses_table
20. create_loyalty_points_table
21. create_payment_methods_table
22. create_cash_registers_table
23. create_cash_sessions_table
24. create_cash_movements_table
25. create_orders_table
26. create_order_items_table
27. create_order_item_modifiers_table
28. create_order_discounts_table
29. create_order_taxes_table
30. create_payments_table
31. create_tips_table
32. create_dian_resolutions_table
33. create_invoices_table
34. create_invoice_lines_table
35. create_credit_notes_table
36. create_debit_notes_table
37. create_dian_documents_table
38. create_ingredients_table
39. create_recipes_table
40. create_recipe_items_table
41. create_stock_movements_table
42. create_suppliers_table
43. create_purchase_orders_table
44. create_purchase_order_items_table
45. create_printers_table
46. create_print_jobs_table
47. create_taxes_table
48. create_settings_table
49. create_audit_logs_table
50. create_notifications_table
```

---

## Campos Clave de Tablas

### customers
```
- id
- branch_id
- customer_type (enum: natural, juridica)
- document_type (enum: CC, NIT, CE, PP, TI)
- document_number
- verification_digit (DV para NIT)
- first_name
- last_name
- business_name (razón social)
- email
- phone
- mobile
- tax_regime (enum: simplificado, comun, gran_contribuyente)
- fiscal_responsibilities (JSON: O-13, O-15, O-23, etc.)
- economic_activity (código CIIU)
- notes
- loyalty_points
- loyalty_level
- credit_limit
- credit_balance
- is_active
- created_at
- updated_at
```

### invoices
```
- id
- branch_id
- order_id
- customer_id
- resolution_id
- invoice_number
- prefix
- invoice_type (enum: factura, pos, contingencia)
- issue_date
- issue_time
- due_date
- payment_form (enum: contado, credito)
- payment_method_code
- currency_code (COP)
- exchange_rate
- subtotal
- total_discount
- total_tax_iva
- total_tax_inc (impoconsumo)
- total_tax_other
- total_withholdings
- total
- notes
- cufe (Código Único de Factura Electrónica)
- qr_data
- dian_status (enum: pending, sent, approved, rejected)
- dian_response
- dian_track_id
- xml_path
- pdf_path
- email_sent_at
- created_at
- updated_at
- deleted_at (soft delete)
```

### dian_resolutions
```
- id
- branch_id
- resolution_number
- resolution_date
- prefix
- range_from
- range_to
- current_number
- technical_key
- valid_from
- valid_to
- environment (enum: test, production)
- document_type (enum: invoice, credit_note, debit_note)
- is_active
- created_at
- updated_at
```

### dian_documents
```
- id
- documentable_type (Invoice, CreditNote, DebitNote)
- documentable_id
- document_type
- cufe_cude
- xml_content
- signed_xml
- dian_response_xml
- status
- attempts
- last_attempt_at
- error_message
- track_id
- created_at
- updated_at
```

---

## Rutas del Sistema

```
routes/
├── web.php                 # Rutas públicas y auth
├── admin.php               # Panel administrativo
├── pos.php                 # Punto de venta
├── kitchen.php             # Pantalla cocina
├── waiter.php              # App mesero
├── customer.php            # Menú cliente / Self-order
├── api.php                 # API REST v1
└── webhooks.php            # Webhooks externos
```

### Grupos de Rutas

**Admin** (prefix: `/admin`, middleware: `auth`, `role:admin|gerente`)
**POS** (prefix: `/pos`, middleware: `auth`, `permission:pos.access`, `check.cash.session`)
**Kitchen** (prefix: `/kitchen`, middleware: `auth`, `permission:kitchen.access`)
**API** (prefix: `/api/v1`, middleware: `auth:sanctum`, `throttle:api`)

---

## Componentes Livewire

| Componente | Descripción |
|------------|-------------|
| `POS\Main` | Componente principal del POS |
| `POS\ProductGrid` | Grid de productos con búsqueda y filtros |
| `POS\Cart` | Carrito de compras reactivo |
| `POS\TableSelector` | Selector de mesas con mapa |
| `POS\CustomerSelector` | Búsqueda y selección de cliente |
| `POS\PaymentProcessor` | Procesador de pagos múltiples |
| `Kitchen\OrderBoard` | Tablero de pedidos en tiempo real |
| `Tables\FloorMap` | Mapa interactivo de mesas |
| `Admin\DashboardStats` | Estadísticas en tiempo real |
| `Admin\SalesChart` | Gráfico de ventas |
| `Admin\RealTimeOrders` | Monitor de órdenes |
| `Inventory\StockTable` | Tabla de inventario con alertas |
| `Reports\ChartContainer` | Contenedor de gráficos dinámicos |

---

## Servicios DIAN - Flujo de Facturación

### Flujo Principal

1. **Crear Factura** → `InvoiceService::create()`
2. **Calcular Impuestos** → `InvoiceCalculationService::calculate()`
3. **Generar XML** → `DIANXMLGenerator::generate()`
4. **Firmar XML** → `DIANSignatureService::sign()`
5. **Generar CUFE** → `DIANCUFEGenerator::generate()`
6. **Enviar a DIAN** → `DIANService::send()` (Job en cola)
7. **Procesar Respuesta** → `DIANService::processResponse()`
8. **Generar QR** → `DIANQRGenerator::generate()`
9. **Generar PDF** → `InvoiceService::generatePDF()`
10. **Notificar Cliente** → `NotificationService::sendInvoice()`

### Estructura XML UBL 2.1

El servicio `DIANXMLGenerator` debe generar XML según:
- Resolución 000042 de 2020
- Anexo Técnico Factura Electrónica v1.9
- Estándar UBL 2.1

### Endpoints DIAN

| Ambiente | URL |
|----------|-----|
| Habilitación | `https://vpfe-hab.dian.gov.co/WcfDianCustomerServices.svc` |
| Producción | `https://vpfe.dian.gov.co/WcfDianCustomerServices.svc` |

---

## Paquetes Laravel Recomendados

| Paquete | Uso |
|---------|-----|
| `spatie/laravel-permission` | Roles y permisos |
| `spatie/laravel-activitylog` | Auditoría |
| `spatie/laravel-medialibrary` | Gestión de imágenes |
| `spatie/laravel-backup` | Respaldos |
| `barryvdh/laravel-dompdf` | PDFs |
| `maatwebsite/excel` | Exportación Excel |
| `livewire/livewire` | Componentes reactivos |
| `simplesoftwareio/simple-qrcode` | Códigos QR |
| `mike42/escpos-php` | Impresión tickets |
| `robrichards/xmlseclibs` | Firma XML |
| `laravel/sanctum` | API auth |
| `pusher/pusher-php-server` | Websockets |

---

## Variables de Entorno

```env
# App
APP_NAME="POS Restaurante"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://pos.mirestaurante.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_restaurante
DB_USERNAME=
DB_PASSWORD=

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Queue
QUEUE_CONNECTION=redis

# Broadcast
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=

# DIAN
DIAN_ENVIRONMENT=production
DIAN_NIT=
DIAN_SOFTWARE_ID=
DIAN_SOFTWARE_PIN=
DIAN_CERTIFICATE_PATH=
DIAN_CERTIFICATE_PASSWORD=
DIAN_TEST_SET_ID=

# Restaurante
RESTAURANT_NAME=
RESTAURANT_NIT=
RESTAURANT_ADDRESS=
RESTAURANT_CITY=
RESTAURANT_PHONE=
RESTAURANT_EMAIL=

# Fiscal
TAX_IVA_PERCENTAGE=19
TAX_IMPOCONSUMO_PERCENTAGE=8
CURRENCY=COP
TIMEZONE=America/Bogota

# Impresoras
PRINTER_RECEIPT_IP=
PRINTER_KITCHEN_IP=
PRINTER_BAR_IP=

# Integraciones
RAPPI_API_KEY=
IFOOD_CLIENT_ID=
IFOOD_CLIENT_SECRET=
UBEREATS_CLIENT_ID=

# WhatsApp
WHATSAPP_API_URL=
WHATSAPP_API_TOKEN=

# Notificaciones
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
```

---

## Comandos Artisan

| Comando | Descripción |
|---------|-------------|
| `pos:open-day` | Iniciar día operativo |
| `pos:close-day` | Cerrar día operativo |
| `pos:close-sessions` | Cerrar sesiones de caja abiertas |
| `dian:sync` | Sincronizar documentos con DIAN |
| `dian:retry-failed` | Reintentar documentos fallidos |
| `dian:check-resolutions` | Verificar vigencia de resoluciones |
| `reports:daily` | Generar reporte diario |
| `reports:send-scheduled` | Enviar reportes programados |
| `inventory:check-stock` | Verificar niveles de stock |
| `inventory:alerts` | Enviar alertas de stock bajo |
| `backup:run` | Ejecutar respaldo |
| `audit:clean` | Limpiar logs antiguos |

---

## Tareas Programadas (Scheduler)

```php
// En Console/Kernel.php

$schedule->command('dian:sync')->everyFiveMinutes();
$schedule->command('dian:retry-failed')->hourly();
$schedule->command('dian:check-resolutions')->daily();
$schedule->command('inventory:check-stock')->dailyAt('06:00');
$schedule->command('reports:daily')->dailyAt('23:30');
$schedule->command('backup:run')->dailyAt('02:00');
$schedule->command('audit:clean')->weekly();
$schedule->command('pos:close-sessions')->dailyAt('04:00');
```

---

## Websockets - Eventos en Tiempo Real

| Canal | Evento | Uso |
|-------|--------|-----|
| `orders` | `OrderCreated` | Nueva orden en cocina |
| `orders` | `OrderItemReady` | Item listo para servir |
| `orders` | `OrderCompleted` | Orden completada |
| `tables.{id}` | `TableStatusChanged` | Cambio estado de mesa |
| `kitchen` | `NewOrderInKitchen` | Nuevo pedido en cocina |
| `cash.{id}` | `PaymentReceived` | Pago procesado |
| `inventory` | `LowStockAlert` | Alerta de stock |
| `user.{id}` | `NotificationReceived` | Notificación personal |

---

## Seguridad

- Validación con Form Requests en todos los endpoints
- Policies para autorización de recursos
- Rate limiting en API
- Sanitización de inputs (XSS)
- Prepared statements (SQL Injection)
- HTTPS obligatorio en producción
- Certificado digital seguro para DIAN
- Encriptación de datos sensibles
- Auditoría de acciones críticas
- Backups automáticos encriptados
- 2FA opcional para administradores

---

## Testing

```
tests/
├── Feature/
│   ├── Auth/
│   ├── Admin/
│   │   ├── ProductTest.php
│   │   ├── CategoryTest.php
│   │   └── ...
│   ├── POS/
│   │   ├── OrderTest.php
│   │   ├── PaymentTest.php
│   │   └── ...
│   ├── DIAN/
│   │   ├── InvoiceGenerationTest.php
│   │   ├── XMLGenerationTest.php
│   │   └── DIANSubmissionTest.php
│   └── Api/
│
├── Unit/
│   ├── Services/
│   │   ├── OrderServiceTest.php
│   │   ├── PaymentServiceTest.php
│   │   ├── DIANServiceTest.php
│   │   └── ...
│   ├── Models/
│   └── Helpers/
│
└── Integration/
    └── DIAN/
        └── DIANIntegrationTest.php
```

---

## Deployment

1. Clonar repositorio
2. `composer install --optimize-autoloader --no-dev`
3. `npm ci && npm run build`
4. Configurar `.env`
5. `php artisan key:generate`
6. `php artisan migrate --seed`
7. `php artisan storage:link`
8. `php artisan config:cache`
9. `php artisan route:cache`
10. `php artisan view:cache`
11. `php artisan icons:cache` (si usa Blade Icons)
12. Configurar Supervisor para queues
13. Configurar cron para scheduler
14. Configurar SSL/HTTPS
15. Cargar certificado DIAN
16. Probar en ambiente de habilitación DIAN
17. Activar en producción

---

## Consideraciones Colombia

### Normativa Fiscal
- Resolución 000042 de 2020 (Facturación Electrónica)
- Resolución 000012 de 2021 (Documento Soporte)
- Estatuto Tributario
- Impoconsumo (Ley 1819 de 2016)

### Tipos de Contribuyente
- Persona Natural
- Persona Jurídica
- Gran Contribuyente
- Autorretenedor
- Régimen Simple

### Responsabilidades Fiscales
- O-13: Gran contribuyente
- O-15: Autorretenedor
- O-23: Agente de retención IVA
- O-47: Régimen simple
- R-99-PN: No aplica

### Códigos de Impuestos
- 01: IVA
- 04: Impoconsumo
- 03: ICA

### Formas de Pago DIAN
- 1: Contado
- 2: Crédito

### Métodos de Pago DIAN
- 10: Efectivo
- 48: Tarjeta crédito
- 49: Tarjeta débito
- ZZZ: Otros (transferencia, etc.)

---

**Versión del documento:** 2.0
**Última actualización:** 2025
**Compatibilidad:** PHP 8.3+ | Laravel 11.x
