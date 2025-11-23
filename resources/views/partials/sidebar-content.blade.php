<div class="flex min-h-0 flex-1 flex-col bg-gray-900">
    <!-- Logo -->
    <div class="flex h-16 flex-shrink-0 items-center px-4 bg-gray-800">
        <div class="flex items-center">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <span class="ml-3 text-white font-semibold text-lg">POS Restaurant</span>
        </div>
    </div>

    <!-- Navigation -->
    <div class="flex flex-1 flex-col overflow-y-auto">
        <nav class="flex-1 space-y-1 px-2 py-4">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Dashboard
            </a>

            <!-- POS Link -->
            @can('pos.access')
            <a href="{{ route('pos.index') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-blue-700 hover:text-white bg-blue-600/20">
                <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Ir al POS
            </a>
            @endcan

            <!-- Divider -->
            <div class="pt-4 pb-2">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Operaciones</p>
            </div>

            <!-- Orders -->
            @can('orders.view')
            <a href="{{ route('admin.orders.index') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.orders.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                Órdenes
            </a>
            @endcan

            <!-- Tables -->
            @can('tables.view')
            <a href="{{ route('admin.tables.index') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.tables.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                </svg>
                Mesas
            </a>
            @endcan

            <!-- Cash -->
            @can('cash.view')
            <a href="{{ route('admin.cash.index') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.cash.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Caja
            </a>
            @endcan

            <!-- Divider -->
            <div class="pt-4 pb-2">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Catálogo</p>
            </div>

            <!-- Products -->
            @can('products.view')
            <a href="{{ route('admin.products.index') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.products.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Productos
            </a>
            @endcan

            <!-- Categories -->
            @can('categories.view')
            <a href="{{ route('admin.categories.index') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.categories.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                Categorías
            </a>
            @endcan

            <!-- Customers -->
            @can('customers.view')
            <a href="{{ route('admin.customers.index') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.customers.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Clientes
            </a>
            @endcan

            <!-- Divider -->
            <div class="pt-4 pb-2">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Facturación</p>
            </div>

            <!-- Invoices -->
            @can('invoices.view')
            <a href="{{ route('admin.invoices.index') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.invoices.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Facturas
            </a>
            @endcan

            <!-- Reports -->
            @can('reports.view')
            <a href="{{ route('admin.reports.index') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.reports.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Reportes
            </a>
            @endcan

            <!-- Divider -->
            @can('users.view')
            <div class="pt-4 pb-2">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Administración</p>
            </div>

            <!-- Users -->
            <a href="{{ route('admin.users.index') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Usuarios
            </a>
            @endcan

            <!-- Settings -->
            @can('settings.view')
            <a href="{{ route('admin.settings.index') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.settings.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Configuración
            </a>
            @endcan
        </nav>
    </div>

    <!-- User info -->
    <div class="flex flex-shrink-0 border-t border-gray-800 p-4">
        <div class="flex items-center w-full">
            <div class="flex-shrink-0">
                <div class="w-9 h-9 rounded-full bg-gray-700 flex items-center justify-center">
                    <span class="text-sm font-medium text-white">
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </span>
                </div>
            </div>
            <div class="ml-3 flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">
                    {{ auth()->user()->name ?? 'Usuario' }}
                </p>
                <p class="text-xs text-gray-400 truncate">
                    {{ auth()->user()->position ?? 'Sin cargo' }}
                </p>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="ml-2">
                @csrf
                <button type="submit" class="p-2 text-gray-400 hover:text-white rounded-lg hover:bg-gray-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
