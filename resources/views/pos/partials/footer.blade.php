<footer class="flex-shrink-0 bg-gray-800 border-t border-gray-700">
    <div class="flex items-center justify-between h-10 px-4">
        <!-- Left - Quick Actions -->
        <div class="flex items-center gap-2">
            <button type="button"
                    class="px-3 py-1 text-xs font-medium text-gray-400 hover:text-white hover:bg-gray-700 rounded transition-colors"
                    title="Atajos de teclado">
                <kbd class="font-mono">F1</kbd> Ayuda
            </button>
            <button type="button"
                    class="px-3 py-1 text-xs font-medium text-gray-400 hover:text-white hover:bg-gray-700 rounded transition-colors"
                    title="Nueva orden">
                <kbd class="font-mono">F2</kbd> Nueva
            </button>
            <button type="button"
                    class="px-3 py-1 text-xs font-medium text-gray-400 hover:text-white hover:bg-gray-700 rounded transition-colors"
                    title="Buscar producto">
                <kbd class="font-mono">F3</kbd> Buscar
            </button>
            <button type="button"
                    class="px-3 py-1 text-xs font-medium text-gray-400 hover:text-white hover:bg-gray-700 rounded transition-colors"
                    title="Cobrar">
                <kbd class="font-mono">F12</kbd> Cobrar
            </button>
        </div>

        <!-- Center - Branch Info -->
        <div class="text-xs text-gray-500">
            {{ session('current_branch_name', config('app.name')) }}
        </div>

        <!-- Right - Connection Status -->
        <div class="flex items-center gap-3">
            <!-- Printer Status -->
            <div class="flex items-center gap-1 text-xs text-gray-500"
                 x-data="{ online: true }"
                 title="Estado de impresora">
                <svg class="w-4 h-4" :class="online ? 'text-green-500' : 'text-red-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span class="hidden sm:inline" x-text="online ? 'Impresora OK' : 'Sin impresora'"></span>
            </div>

            <!-- Network Status -->
            <div class="flex items-center gap-1 text-xs"
                 x-data="{ online: navigator.onLine }"
                 x-init="
                    window.addEventListener('online', () => online = true);
                    window.addEventListener('offline', () => online = false);
                 "
                 :class="online ? 'text-green-500' : 'text-red-500'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
                </svg>
                <span class="hidden sm:inline" x-text="online ? 'En línea' : 'Sin conexión'"></span>
            </div>
        </div>
    </div>
</footer>
