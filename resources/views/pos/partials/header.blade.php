<header class="flex-shrink-0 bg-gray-800 border-b border-gray-700">
    <div class="flex items-center justify-between h-14 px-4">
        <!-- Left side -->
        <div class="flex items-center gap-4">
            <!-- Back to Admin -->
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span class="text-sm font-medium hidden sm:inline">Admin</span>
            </a>

            <!-- Divider -->
            <div class="h-6 w-px bg-gray-700"></div>

            <!-- Logo/Brand -->
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="text-white font-semibold text-lg hidden sm:block">POS</span>
            </div>
        </div>

        <!-- Center - Current Order Info -->
        <div class="flex items-center gap-4">
            @isset($currentTable)
            <div class="flex items-center gap-2 bg-blue-600/20 text-blue-400 px-3 py-1.5 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"></path>
                </svg>
                <span class="font-medium">Mesa {{ $currentTable->number }}</span>
            </div>
            @endisset

            @isset($currentOrder)
            <div class="flex items-center gap-2 bg-gray-700 px-3 py-1.5 rounded-lg">
                <span class="text-gray-400 text-sm">Orden:</span>
                <span class="text-white font-mono font-medium">{{ $currentOrder->order_number }}</span>
            </div>
            @endisset
        </div>

        <!-- Right side -->
        <div class="flex items-center gap-3">
            <!-- Cash Register Status -->
            @if(session('cash_session_id'))
            <div class="flex items-center gap-2 text-green-400">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                <span class="text-sm font-medium hidden md:inline">Caja Abierta</span>
            </div>
            @else
            <div class="flex items-center gap-2 text-yellow-400">
                <div class="w-2 h-2 bg-yellow-400 rounded-full"></div>
                <span class="text-sm font-medium hidden md:inline">Caja Cerrada</span>
            </div>
            @endif

            <!-- Divider -->
            <div class="h-6 w-px bg-gray-700"></div>

            <!-- Clock -->
            <div class="text-gray-300 font-mono text-sm"
                 x-data="{ time: '' }"
                 x-init="setInterval(() => time = new Date().toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' }), 1000)"
                 x-text="time">
            </div>

            <!-- User -->
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center">
                    <span class="text-sm font-medium text-white">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                </div>
                <div class="hidden lg:block">
                    <p class="text-sm font-medium text-white">{{ auth()->user()->name ?? 'Usuario' }}</p>
                    <p class="text-xs text-gray-400">{{ auth()->user()->position ?? 'Sin cargo' }}</p>
                </div>
            </div>

            <!-- Logout/Lock -->
            <button type="button"
                    onclick="document.getElementById('lock-screen-modal').classList.remove('hidden')"
                    class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
                    title="Bloquear pantalla">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </button>
        </div>
    </div>
</header>
