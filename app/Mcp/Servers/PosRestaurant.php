<?php

namespace App\Mcp\Servers;

use Laravel\Mcp\Server;

class PosRestaurant extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'POS Restaurante';

    /**
     * The MCP server's version.
     */
    protected string $version = '1.0.0';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = <<<'MARKDOWN'
        # Sistema POS para Restaurantes
        
        Este servidor MCP proporciona acceso completo al sistema POS de restaurante, incluyendo:
        
        - Gestión de productos y categorías
        - Control de mesas y zonas
        - Gestión de órdenes y pedidos
        - Control de caja y sesiones
        - Reportes de ventas
        - Gestión de clientes
        - Facturación electrónica
        
        ## Funcionalidades Principales
        
        - **Productos**: Consultar productos activos, buscar por categoría, obtener detalles
        - **Mesas**: Ver estado de mesas, cambiar estados, asignar/liberar mesas
        - **Órdenes**: Crear órdenes, consultar pendientes, actualizar estados
        - **Caja**: Verificar sesión activa, consultar movimientos, reportes de cierre
        - **Ventas**: Reportes diarios, por período, productos más vendidos
        - **Clientes**: Buscar, crear, consultar historial de compras
        
        Usa los recursos y herramientas disponibles para interactuar con el sistema.
    MARKDOWN;

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [
        //
    ];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Resource>>
     */
    protected array $resources = [
        //
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Prompt>>
     */
    protected array $prompts = [
        //
    ];
}
