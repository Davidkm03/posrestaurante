<x-layouts.app title="Reportes">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Reportes</h1>
                <p class="text-sm text-gray-500">Analiza el rendimiento de tu negocio</p>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Reporte de Ventas -->
        <a href="{{ route('admin.reports.sales') }}" class="block">
            <x-card class="hover:shadow-lg transition-shadow cursor-pointer h-full">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Ventas</h3>
                        <p class="text-sm text-gray-500 mt-1">Análisis de ventas por período</p>
                    </div>
                </div>
            </x-card>
        </a>

        <!-- Reporte de Productos -->
        <a href="{{ route('admin.reports.products') }}" class="block">
            <x-card class="hover:shadow-lg transition-shadow cursor-pointer h-full">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Productos</h3>
                        <p class="text-sm text-gray-500 mt-1">Productos más vendidos</p>
                    </div>
                </div>
            </x-card>
        </a>

        <!-- Reporte de Meseros -->
        <a href="{{ route('admin.reports.waiters') }}" class="block">
            <x-card class="hover:shadow-lg transition-shadow cursor-pointer h-full">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Meseros</h3>
                        <p class="text-sm text-gray-500 mt-1">Rendimiento por mesero</p>
                    </div>
                </div>
            </x-card>
        </a>

        <!-- Reporte de Impuestos -->
        <a href="{{ route('admin.reports.taxes') }}" class="block">
            <x-card class="hover:shadow-lg transition-shadow cursor-pointer h-full">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Impuestos</h3>
                        <p class="text-sm text-gray-500 mt-1">Reporte de IVA y retenciones</p>
                    </div>
                </div>
            </x-card>
        </a>

        <!-- Reporte por Horas -->
        <a href="{{ route('admin.reports.hourly') }}" class="block">
            <x-card class="hover:shadow-lg transition-shadow cursor-pointer h-full">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-red-100 rounded-lg">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Por Horas</h3>
                        <p class="text-sm text-gray-500 mt-1">Ventas por hora del día</p>
                    </div>
                </div>
            </x-card>
        </a>

        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}" class="block">
            <x-card class="hover:shadow-lg transition-shadow cursor-pointer h-full">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-indigo-100 rounded-lg">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 12a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1v-7z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Dashboard</h3>
                        <p class="text-sm text-gray-500 mt-1">Vista general del negocio</p>
                    </div>
                </div>
            </x-card>
        </a>
    </div>

    <!-- Información adicional -->
    <x-card class="mt-6">
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h4 class="font-medium text-gray-900">Acerca de los reportes</h4>
                <p class="text-sm text-gray-600 mt-1">
                    Los reportes se generan en tiempo real basándose en los datos de tu sucursal actual. 
                    Puedes filtrar por fechas y exportar los resultados en diferentes formatos.
                </p>
            </div>
        </div>
    </x-card>
</x-layouts.app>
