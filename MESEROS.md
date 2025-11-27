# 👨‍🍳 Vista de Meseros - Documentación

## 📱 Interfaz Optimizada para Tablets y Móviles

La vista de meseros está diseñada específicamente para dispositivos táctiles (tablets y móviles), permitiendo a los meseros gestionar órdenes de forma rápida y eficiente sin necesidad de acceso a caja.

## 🔐 Acceso

**URL:** `/waiter`

**Credenciales de prueba:**
- **Email:** `mesero@pos.com`
- **Contraseña:** `password`

## ✨ Características Principales

### 1. Vista de Mesas (`/waiter`)
- **Grid responsive** de todas las mesas organizadas por zonas
- **Código de colores** por estado:
  - 🟢 **Verde:** Mesa disponible
  - 🔵 **Azul:** Mesa ocupada
  - 🟡 **Amarillo:** Mesa reservada
  - ⚪ **Gris:** Mesa inactiva

- **Información en tiempo real:**
  - Número de mesa y capacidad
  - Cantidad de comensales (si está ocupada)
  - Tiempo transcurrido desde que se ocupó
  - Zona a la que pertenece

- **Resumen de órdenes activas** en la parte superior
  - Total de órdenes del mesero
  - Cantidad de órdenes listas para servir

### 2. Mis Órdenes (`/waiter/orders`)
- **Lista completa** de todas las órdenes del mesero
- **Filtrado por estado:**
  - 🟡 Pendiente
  - 🔵 En Preparación
  - 🟢 Lista
  - ⚪ Servida

- **Detalles de cada orden:**
  - Número de orden
  - Mesa y zona
  - Cantidad de comensales
  - Items con modificadores y notas
  - Total de la orden
  - Tiempo transcurrido

- **Acción rápida:**
  - Botón "Marcar como Servido" para órdenes listas

## 🎯 Flujo de Trabajo

### Tomar una Nueva Orden

1. **Seleccionar mesa** desde el grid
2. **Configurar comensales** (+ / -)
3. **Seleccionar categoría** o usar búsqueda
4. **Agregar productos** tocando las tarjetas
5. **Ajustar cantidades** con + / -
6. **Agregar notas** especiales por producto o general
7. **Enviar a Cocina** presionando el botón verde
8. La mesa cambia a **estado ocupado (azul)**

### Agregar Items a Orden Existente

1. **Tocar mesa ocupada** (azul)
2. Verás los **items ya pedidos** con borde azul y etiqueta "Ya pedido"
3. **Agregar nuevos productos** normalmente
4. Solo los **nuevos items** aparecen editables
5. **Presionar "Agregar a Orden"**
6. Los nuevos items **se envían a cocina** automáticamente
7. La orden **se actualiza** con el nuevo total

### Servir una Orden

1. Ir a **"Mis Órdenes"**
2. Encontrar la orden con estado **"Lista"** (verde)
3. Presionar **"Marcar como Servido"**
4. La orden cambia a estado "Servida"

## 📱 Interfaz de Toma de Orden

### Panel de Productos (Izquierda)
- **Barra de búsqueda** en la parte superior
- **Categorías horizontales** con scroll
- **Grid de productos** responsive (2-5 columnas)
- **Tarjetas de producto** con:
  - Imagen o placeholder
  - Nombre
  - Precio
  - Toque para agregar al carrito

### Panel de Carrito (Derecha)
- **Contador de items** en el header
- **Lista de productos** agregados:
  - Items existentes con **borde azul** y etiqueta
  - Items nuevos **totalmente editables**
  - Botones + / - para cantidad
  - Campo de notas por item
  - Botón X para eliminar
- **Notas generales** de la orden
- **Resumen de totales**:
  - Subtotal
  - IVA incluido
  - Total
- **Botón grande de acción**:
  - "Enviar a Cocina" (nueva orden)
  - "Agregar a Orden" (orden existente)

## 📱 Optimizaciones Táctiles

- ✅ **Botones grandes** (mínimo 44x44px) fáciles de tocar
- ✅ **Transición de escala** al presionar (feedback visual)
- ✅ **Sin hover states** innecesarios en móvil
- ✅ **Layout adaptativo:**
  - Portrait: 2-3 mesas por fila
  - Landscape: 4-6 mesas por fila
  - Desktop: hasta 6 mesas por fila

- ✅ **Prevención de zoom accidental:**
  - `maximum-scale=1, user-scalable=no` en viewport
  - Diseñado para tablets en modo standalone

## 🚀 Funcionalidades Completadas

### ✅ Implementado
- [x] Vista de mesas con estados en tiempo real
- [x] Toma de órdenes completa (nueva orden)
- [x] Agregar items a órdenes existentes
- [x] Grid de productos por categorías
- [x] Búsqueda de productos
- [x] Ajuste de cantidades y notas
- [x] Contador de comensales
- [x] Envío a cocina sin cobro
- [x] Ver mis órdenes activas
- [x] Marcar órdenes como servidas
- [x] Interfaz optimizada para tablets
- [x] Diseño responsive touch-friendly

### 🔜 Próximas Funcionalidades

- [ ] Modificadores de productos (extras, sin cebolla, etc.)
- [ ] Editar órdenes ya enviadas (antes de cocina)
- [ ] Dividir cuentas por comensal
- [ ] Ver historial completo de órdenes
- [ ] Notificaciones push cuando una orden está lista
- [ ] Modo offline básico (PWA)
- [ ] Chat entre meseros y cocina
- [ ] Transferir mesas entre meseros
- [ ] Propinas sugeridas
- [ ] Estadísticas personales del mesero

## 🎨 Diseño

### Layout
- **Header fijo** con navegación por tabs
- **Sin sidebar** para maximizar espacio
- **Logout rápido** siempre visible
- **Info contextual:** sucursal, hora, nombre del mesero

### Colores
- Usa la misma paleta del sistema principal
- Estados diferenciados por color
- Buen contraste para legibilidad en exteriores

## 🔧 Configuración Recomendada

### Para Tablets (iPad, Android)

1. **Agregar a pantalla de inicio** (PWA)
2. **Orientación:** Landscape preferido, pero funciona en Portrait
3. **Resolución mínima:** 768px × 1024px
4. **Navegador:** Safari (iOS), Chrome (Android)

### Para Móviles

1. **Orientación:** Portrait
2. **Funciona en pantallas desde:** 320px de ancho
3. **Ideal para emergencias** o restaurantes pequeños

## 📊 Permisos y Seguridad

- ✅ Solo puede ver **sus propias órdenes**
- ✅ Solo puede **marcar como servido** órdenes listas
- ✅ **No tiene acceso** a:
  - Cobros y caja
  - Configuración del sistema
  - Órdenes de otros meseros
  - Reportes administrativos

## 💡 Tips de Uso

1. **Mantén actualizada** la vista de "Mis Órdenes" para ver cuando algo está listo
2. **Usa el resumen** en la vista de mesas para saber cuántas órdenes tienes pendientes
3. **Verifica la mesa** antes de tomar la orden (número correcto)
4. **Comunica el tiempo estimado** según el estado en cocina

## 🐛 Problemas Conocidos

- Los modificadores de productos aún no están implementados (próxima fase)
- Las notificaciones push requieren configuración adicional
- El modo offline no está disponible aún

## ✨ Características Especiales

### Detección de Orden Existente
Cuando tocas una **mesa ocupada**, el sistema automáticamente:
- ✅ Carga la orden actual
- ✅ Muestra los items ya pedidos con **borde azul**
- ✅ Los items existentes son **solo lectura** (no editables)
- ✅ Solo los **nuevos items** son modificables
- ✅ Al enviar, **solo los nuevos items van a cocina**
- ✅ El total **se actualiza automáticamente**

### Gestión Inteligente del Carrito
- **Items existentes** (ya en cocina):
  - Borde azul distintivo
  - Etiqueta "Ya pedido"
  - Solo visualización (cantidad y notas fijas)
  - Se muestran para contexto

- **Items nuevos** (por agregar):
  - Borde normal
  - Totalmente editables
  - Botones + / - activos
  - Campo de notas editable
  - Botón X para eliminar

### Sincronización con el Sistema
- ✅ Las órdenes creadas por meseros **aparecen en el POS**
- ✅ Se **registran en el sistema de caja**
- ✅ Se **envían automáticamente a cocina**
- ✅ El **cajero puede cobrarlas** cuando estén listas
- ✅ Las mesas cambian de **estado automáticamente**

## 📞 Soporte

Si encuentras algún problema o tienes sugerencias:
1. Contacta al administrador del sistema
2. Reporta el error con detalles (qué mesa, qué orden, qué hora)
