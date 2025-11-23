<header class="flex-shrink-0 bg-gray-800 border-b border-gray-700">
    <div class="flex items-center justify-between h-16 px-6">
        <!-- Left - Title -->
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white">COCINA</h1>
                    <p class="text-xs text-gray-400">{{ session('current_branch_name', 'Sucursal Principal') }}</p>
                </div>
            </div>
        </div>

        <!-- Center - Stats -->
        <div class="flex items-center gap-6">
            <!-- Pending Orders -->
            <div class="text-center">
                <p class="text-3xl font-bold text-yellow-400" id="pending-count">0</p>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Pendientes</p>
            </div>

            <!-- In Progress -->
            <div class="text-center">
                <p class="text-3xl font-bold text-blue-400" id="progress-count">0</p>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Preparando</p>
            </div>

            <!-- Ready -->
            <div class="text-center">
                <p class="text-3xl font-bold text-green-400" id="ready-count">0</p>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Listos</p>
            </div>

            <!-- Average Time -->
            <div class="text-center border-l border-gray-700 pl-6">
                <p class="text-3xl font-bold text-white" id="avg-time">0</p>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Min. Promedio</p>
            </div>
        </div>

        <!-- Right - Controls -->
        <div class="flex items-center gap-4">
            <!-- View Toggle -->
            <div class="flex rounded-lg bg-gray-700 p-1">
                <button type="button"
                        class="px-3 py-1.5 text-sm font-medium rounded-md bg-gray-600 text-white"
                        data-view="grid">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </button>
                <button type="button"
                        class="px-3 py-1.5 text-sm font-medium rounded-md text-gray-400 hover:text-white"
                        data-view="list">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Sound Toggle -->
            <button type="button"
                    class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-700"
                    x-data="{ muted: false }"
                    @click="muted = !muted"
                    :class="muted ? 'text-red-400' : 'text-green-400'">
                <svg x-show="!muted" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                </svg>
                <svg x-show="muted" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"></path>
                </svg>
            </button>

            <!-- Fullscreen -->
            <button type="button"
                    class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-700"
                    onclick="document.documentElement.requestFullscreen()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                </svg>
            </button>

            <!-- Clock -->
            <div class="text-right">
                <p class="text-2xl font-bold text-white font-mono"
                   x-data="{ time: '' }"
                   x-init="setInterval(() => time = new Date().toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', second: '2-digit' }), 1000)"
                   x-text="time">
                </p>
                <p class="text-xs text-gray-400"
                   x-data="{ date: '' }"
                   x-init="date = new Date().toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'short' })"
                   x-text="date">
                </p>
            </div>
        </div>
    </div>
</header>
