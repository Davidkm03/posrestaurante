{{-- Header de Cocina --}}
<header class="flex-shrink-0 bg-gray-800 border-b border-gray-700 h-14">
    <div class="flex items-center justify-between h-full px-4">
        {{-- Logo --}}
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-orange-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                </svg>
            </div>
            <span class="text-lg font-bold">COCINA</span>
        </div>

        {{-- Stats --}}
        <div class="flex items-center gap-8">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-yellow-400 rounded-full animate-pulse"></span>
                <span class="text-2xl font-bold text-yellow-400">{{ $stats['pending'] }}</span>
                <span class="text-xs text-gray-400">Pendientes</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-blue-400 rounded-full animate-pulse"></span>
                <span class="text-2xl font-bold text-blue-400">{{ $stats['preparing'] }}</span>
                <span class="text-xs text-gray-400">Preparando</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-green-400 rounded-full"></span>
                <span class="text-2xl font-bold text-green-400">{{ $stats['ready'] }}</span>
                <span class="text-xs text-gray-400">Listos</span>
            </div>
            <div class="border-l border-gray-600 pl-4">
                <span class="text-2xl font-bold">{{ $stats['avg_time'] }}</span>
                <span class="text-xs text-gray-400">min prom</span>
            </div>
        </div>

        {{-- Controles --}}
        <div class="flex items-center gap-3">
            <button onclick="document.documentElement.requestFullscreen()" 
                    class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-700" title="Pantalla completa">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                </svg>
            </button>
            <div class="text-right" x-data="{ time: '' }" x-init="setInterval(() => time = new Date().toLocaleTimeString('es-CO', {hour:'2-digit', minute:'2-digit'}), 1000)">
                <span class="text-xl font-bold font-mono" x-text="time"></span>
            </div>
        </div>
    </div>
</header>
