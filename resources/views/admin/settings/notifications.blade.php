<x-layouts.app title="Admin">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Configuración de Notificaciones
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.settings.notifications.update') }}">
                @csrf

                <!-- Email -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                    <div class="p-4 border-b bg-gray-50 flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Notificaciones por Email</h3>
                            <p class="text-sm text-gray-500">Configura qué eventos envían correos</p>
                        </div>
                    </div>
                    <div class="p-4 space-y-4">
                        <label class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                            <div>
                                <span class="font-medium text-gray-900">Cierre de caja</span>
                                <p class="text-sm text-gray-500">Enviar resumen al cerrar caja</p>
                            </div>
                            <input type="checkbox" name="email_cash_close" class="rounded text-blue-600" checked>
                        </label>
                        <label class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                            <div>
                                <span class="font-medium text-gray-900">Stock bajo</span>
                                <p class="text-sm text-gray-500">Alertar cuando productos estén bajos</p>
                            </div>
                            <input type="checkbox" name="email_low_stock" class="rounded text-blue-600" checked>
                        </label>
                        <label class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                            <div>
                                <span class="font-medium text-gray-900">Reservaciones</span>
                                <p class="text-sm text-gray-500">Notificar nuevas reservaciones</p>
                            </div>
                            <input type="checkbox" name="email_reservations" class="rounded text-blue-600">
                        </label>
                        <label class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                            <div>
                                <span class="font-medium text-gray-900">Reportes diarios</span>
                                <p class="text-sm text-gray-500">Resumen de ventas cada día</p>
                            </div>
                            <input type="checkbox" name="email_daily_report" class="rounded text-blue-600">
                        </label>
                    </div>
                </div>

                <!-- Sonidos -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                    <div class="p-4 border-b bg-gray-50 flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Alertas de Sonido</h3>
                            <p class="text-sm text-gray-500">Sonidos en la aplicación</p>
                        </div>
                    </div>
                    <div class="p-4 space-y-4">
                        <label class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                            <div>
                                <span class="font-medium text-gray-900">Nueva orden en cocina</span>
                                <p class="text-sm text-gray-500">Sonido al recibir comanda</p>
                            </div>
                            <input type="checkbox" name="sound_kitchen_order" class="rounded text-blue-600" checked>
                        </label>
                        <label class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                            <div>
                                <span class="font-medium text-gray-900">Orden lista</span>
                                <p class="text-sm text-gray-500">Notificar cuando orden está lista</p>
                            </div>
                            <input type="checkbox" name="sound_order_ready" class="rounded text-blue-600" checked>
                        </label>
                        <label class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                            <div>
                                <span class="font-medium text-gray-900">Pago recibido</span>
                                <p class="text-sm text-gray-500">Confirmación de pago exitoso</p>
                            </div>
                            <input type="checkbox" name="sound_payment" class="rounded text-blue-600">
                        </label>
                    </div>
                </div>

                <!-- Push -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                    <div class="p-4 border-b bg-gray-50 flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Notificaciones Push</h3>
                            <p class="text-sm text-gray-500">Alertas en dispositivos móviles</p>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="p-4 bg-gray-50 rounded-lg text-center">
                            <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-sm text-gray-600">Próximamente disponible</p>
                            <p class="text-xs text-gray-500 mt-1">Requiere app móvil instalada</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Guardar Configuración
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
