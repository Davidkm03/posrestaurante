<header class="sticky top-0 z-10 flex h-16 flex-shrink-0 bg-white shadow">
    <button type="button"
            @click="sidebarOpen = true"
            class="border-r border-gray-200 px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 lg:hidden">
        <span class="sr-only">Open sidebar</span>
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
        </svg>
    </button>

    <div class="flex flex-1 justify-between px-4">
        <div class="flex flex-1 items-center">
            <!-- Branch Selector -->
            @if(auth()->user()?->branches->count() > 1)
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        type="button"
                        class="flex items-center gap-2 rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">
                    <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>{{ session('current_branch_name', 'Seleccionar Sucursal') }}</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div x-show="open"
                     @click.away="open = false"
                     x-transition
                     class="absolute left-0 z-10 mt-2 w-56 origin-top-left rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                    <div class="py-1">
                        @foreach(auth()->user()->branches as $branch)
                        <a href="{{ route('admin.switch-branch', $branch) }}"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            {{ $branch->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="ml-4 flex items-center gap-4">
            <!-- Kitchen Display Link -->
            @can('kitchen.access')
            <a href="{{ route('kitchen.index') }}"
               class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700"
               title="Pantalla Cocina">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </a>
            @endcan

            <!-- Notifications -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        type="button"
                        class="relative rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                    <span class="sr-only">Ver notificaciones</span>
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <!-- Notification Badge -->
                    <span class="absolute top-1 right-1 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                </button>

                <div x-show="open"
                     @click.away="open = false"
                     x-transition
                     class="absolute right-0 z-10 mt-2 w-80 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-900">Notificaciones</p>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <p class="px-4 py-8 text-center text-sm text-gray-500">
                            No hay notificaciones nuevas
                        </p>
                    </div>
                </div>
            </div>

            <!-- Current Date/Time -->
            <div class="hidden md:flex items-center text-sm text-gray-500">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span x-data="{ time: '' }"
                      x-init="setInterval(() => time = new Date().toLocaleString('es-CO', { weekday: 'short', hour: '2-digit', minute: '2-digit' }), 1000)"
                      x-text="time">
                </span>
            </div>
        </div>
    </div>
</header>
