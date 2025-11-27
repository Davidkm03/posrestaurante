# Configuración de Laravel MCP para POS Restaurante

## ✅ Instalación Completada

Laravel MCP ha sido instalado y configurado exitosamente en tu proyecto.

## 📁 Estructura Creada

```
app/Mcp/
├── Servers/
│   └── PosRestaurant.php      # Servidor MCP principal
├── Resources/
│   └── ProductosActivos.php   # Recurso para consultar productos
├── Tools/
│   └── BuscarCliente.php      # Herramienta para buscar clientes
└── Prompts/
    └── CrearOrden.php         # Prompt para crear órdenes
```

## 🚀 Próximos Pasos

### 1. Personalizar los Componentes

#### **Recurso: ProductosActivos**
Edita `app/Mcp/Resources/ProductosActivos.php` para exponer los productos:

```php
public function handle(): string
{
    $productos = \App\Models\Product::with('category')
        ->active()
        ->get()
        ->map(fn($p) => [
            'id' => $p->id,
            'nombre' => $p->name,
            'categoria' => $p->category->name,
            'precio' => $p->price,
        ]);
    
    return json_encode($productos, JSON_PRETTY_PRINT);
}
```

#### **Tool: BuscarCliente**
Edita `app/Mcp/Tools/BuscarCliente.php` para implementar la búsqueda:

```php
public function handle(string $termino): string
{
    $clientes = \App\Models\Customer::where('name', 'like', "%{$termino}%")
        ->orWhere('document', 'like', "%{$termino}%")
        ->limit(10)
        ->get();
    
    return json_encode($clientes, JSON_PRETTY_PRINT);
}
```

#### **Prompt: CrearOrden**
Edita `app/Mcp/Prompts/CrearOrden.php` para guiar la creación de órdenes.

### 2. Registrar en el Servidor

Actualiza `app/Mcp/Servers/PosRestaurant.php`:

```php
protected array $tools = [
    \App\Mcp\Tools\BuscarCliente::class,
];

protected array $resources = [
    \App\Mcp\Resources\ProductosActivos::class,
];

protected array $prompts = [
    \App\Mcp\Prompts\CrearOrden::class,
];
```

### 3. Publicar el Servidor

Laravel MCP puede servir tu servidor de dos formas:

#### Opción A: Servidor Web (Desarrollo)
Añade a `routes/ai.php`:

```php
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp/pos', \App\Mcp\Servers\PosRestaurant::class);
```

Luego accede desde: `http://localhost:8000/mcp/pos`

#### Opción B: Servidor Standalone (Producción)
Crea un ejecutable `mcp-server`:

```bash
php artisan mcp:publish PosRestaurant
```

### 4. Configurar Claude Desktop

Edita el archivo de configuración de Claude Desktop:

**macOS**: `~/Library/Application Support/Claude/claude_desktop_config.json`

```json
{
  "mcpServers": {
    "posrestaurante": {
      "command": "php",
      "args": [
        "/Users/admin/posrestaurante/artisan",
        "mcp:serve",
        "PosRestaurant"
      ]
    }
  }
}
```

**Importante**: Ajusta la ruta absoluta a tu proyecto.

### 5. Reiniciar Claude Desktop

Cierra completamente Claude Desktop y vuelve a abrirlo.

## 📋 Comandos Útiles

```bash
# Crear nuevos recursos
php artisan make:mcp-resource NombreRecurso

# Crear nuevas herramientas
php artisan make:mcp-tool NombreHerramienta

# Crear nuevos prompts
php artisan make:mcp-prompt NombrePrompt

# Listar servidores MCP
php artisan mcp:list

# Servir un servidor MCP
php artisan mcp:serve PosRestaurant
```

## 💡 Ideas de Recursos y Tools

### Recursos Sugeridos
- **MesasDisponibles**: Lista de mesas libres
- **OrdenesPendientes**: Órdenes que están en preparación
- **CajaActual**: Estado de la sesión de caja abierta
- **VentasDelDia**: Resumen de ventas del día
- **InventarioBajo**: Productos con stock bajo

### Tools Sugeridos
- **CrearCliente**: Registrar un nuevo cliente
- **ActualizarMesa**: Cambiar estado de una mesa
- **ConsultarVentas**: Obtener reporte de ventas por período
- **CerrarCaja**: Cerrar la sesión de caja actual
- **AnularOrden**: Anular una orden existente

### Prompts Sugeridos
- **ReporteVentas**: Guía para generar reportes
- **ConfigurarProducto**: Ayuda para crear/editar productos
- **GestionarMesas**: Flujo de trabajo para mesas
- **ProcesarPago**: Pasos para procesar un pago

## 🔒 Seguridad

⚠️ **Importante**:
- Este servidor tiene acceso directo a la base de datos
- Úsalo solo en desarrollo local
- Para producción, implementa autenticación y autorización
- Valida todos los inputs en los Tools
- No expongas datos sensibles en los Resources

## 📚 Documentación Oficial

- [Laravel MCP Docs](https://laravel.com/docs/mcp)
- [Model Context Protocol](https://modelcontextprotocol.io/)
- [GitHub - Laravel MCP](https://github.com/laravel/mcp)

## 🎯 Estado Actual

- ✅ Paquete Laravel MCP instalado
- ✅ Configuración publicada
- ✅ Servidor PosRestaurant creado
- ✅ Componentes base creados (Resource, Tool, Prompt)
- ⏳ Pendiente: Personalizar componentes
- ⏳ Pendiente: Registrar en Claude Desktop
- ⏳ Pendiente: Probar integración

## 🔧 Troubleshooting

### Error: "Server not found"
Verifica que la ruta en `claude_desktop_config.json` sea absoluta y correcta.

### Error: "Command not found"
Asegúrate de que PHP esté en el PATH del sistema.

### Error: "Connection refused"
El servidor MCP debe estar corriendo. Usa `php artisan mcp:serve PosRestaurant`.

### Claude Desktop no muestra el servidor
1. Verifica la sintaxis del JSON de configuración
2. Reinicia completamente Claude Desktop
3. Revisa Developer Tools (Help > Developer Tools)

Edita el archivo de configuración de Claude Desktop:

**macOS**: `~/Library/Application Support/Claude/claude_desktop_config.json`
**Windows**: `%APPDATA%\Claude\claude_desktop_config.json`

Agrega la siguiente configuración:

```json
{
  "mcpServers": {
    "posrestaurante": {
      "command": "php",
      "args": [
        "/Users/admin/posrestaurante/mcp"
      ],
      "env": {
        "APP_ENV": "local"
      }
    }
  }
}
```

**Importante**: Asegúrate de actualizar la ruta `/Users/admin/posrestaurante/mcp` con la ruta absoluta correcta de tu proyecto.

### 3. Reiniciar Claude Desktop

Cierra completamente Claude Desktop y vuelve a abrirlo para que cargue el servidor MCP.

## Recursos Disponibles

El servidor MCP proporciona acceso a:

### Prompts
- **crear-orden**: Ayuda a crear una nueva orden en el sistema POS
- **buscar-producto**: Busca productos en el inventario
- **reporte-ventas**: Genera reportes de ventas
- **estado-caja**: Muestra el estado de la caja registradora
- **gestion-mesas**: Gestiona el estado de las mesas

### Recursos (Datos en tiempo real)
- **productos**: Todos los productos activos con sus categorías
- **categorias**: Todas las categorías activas
- **mesas**: Estado de todas las mesas
- **ordenes-pendientes**: Órdenes pendientes y en preparación
- **caja-actual**: Sesión de caja actual abierta

### Tools (Operaciones)
- **crear-producto**: Crea un nuevo producto
- **buscar-cliente**: Busca clientes por nombre, documento o teléfono
- **actualizar-estado-mesa**: Cambia el estado de una mesa
- **ventas-del-dia**: Obtiene las ventas totales del día actual

## Ejemplos de Uso

Una vez configurado, puedes pedirle a Claude:

1. "Muéstrame todos los productos disponibles"
2. "¿Cuántas órdenes están pendientes?"
3. "¿Cuál es el estado actual de la caja?"
4. "Busca el cliente con documento 123456789"
5. "Muéstrame las ventas del día"
6. "Cambia la mesa 5 a estado ocupado"

## Verificar Funcionamiento

Para probar el servidor MCP manualmente:

```bash
php mcp
```

El servidor debe iniciar sin errores. Presiona Ctrl+C para detenerlo.

## Seguridad

⚠️ **Importante**: Este servidor MCP tiene acceso directo a tu base de datos. Recomendaciones:

1. Úsalo solo en entorno de desarrollo
2. No expongas el servidor a internet
3. Para producción, considera implementar autenticación OAuth
4. Revisa y personaliza los recursos y herramientas según tus necesidades

## Personalización

El archivo `mcp` en la raíz del proyecto contiene toda la configuración. Puedes:

- Agregar más prompts
- Exponer más recursos (tablas, relaciones)
- Crear nuevas herramientas para operaciones específicas
- Agregar validaciones y permisos

## Troubleshooting

### El servidor no inicia
- Verifica que PHP esté en el PATH
- Asegúrate de que la base de datos esté accesible
- Revisa los logs de Laravel: `storage/logs/laravel.log`

### Claude Desktop no muestra el servidor
- Verifica que la ruta en `claude_desktop_config.json` sea absoluta y correcta
- Reinicia completamente Claude Desktop
- Revisa la consola de desarrollador de Claude (Help > Developer Tools)

### Permisos insuficientes
```bash
chmod +x mcp
```

## Documentación Oficial

- [Laravel MCP](https://github.com/laravel/mcp)
- [Model Context Protocol](https://modelcontextprotocol.io/)
