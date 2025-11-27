<x-layouts.app>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Gestión de Caja</h1>
                    <p class="mt-2 text-base text-gray-600">Administra las sesiones de caja y cajas registradoras</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('admin.cash.sessions') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Ver Historial Completo
                    </a>
                </div>
            </div>

            <!-- Current Session or Open Cash -->
            @if($currentSession)
            <!-- Active Session Card -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl shadow-xl p-8 mb-8 text-white">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold">Caja Abierta</h2>
                            <p class="text-green-100">{{ $currentSession->cashRegister->name }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-white/20">
                        <span class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></span>
                        En operación
                    </span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="bg-white/10 rounded-xl p-4">
                        <p class="text-green-100 text-sm font-medium">Apertura</p>
                        <p class="text-2xl font-bold mt-1">{{ $currentSession->opened_at->format('H:i') }}</p>
                        <p class="text-green-100 text-sm">{{ $currentSession->opened_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4">
                        <p class="text-green-100 text-sm font-medium">Monto Inicial</p>
                        <p class="text-2xl font-bold mt-1">${{ number_format($currentSession->opening_amount, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4">
                        <p class="text-green-100 text-sm font-medium">Tiempo Activo</p>
                        <p class="text-2xl font-bold mt-1">{{ $currentSession->opened_at->diffForHumans(null, true) }}</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4">
                        <p class="text-green-100 text-sm font-medium">Cajero</p>
                        <p class="text-2xl font-bold mt-1 truncate">{{ $currentSession->user->name }}</p>
                    </div>
                </div>

                <div class="mt-8 flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('admin.cash.sessions.show', $currentSession) }}" 
                       class="flex-1 inline-flex items-center justify-center px-6 py-4 border-2 border-white rounded-xl text-lg font-semibold hover:bg-white/10 transition-colors">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Ver Detalle
                    </a>
                    <button type="button" onclick="openCloseModal()" 
                            class="flex-1 inline-flex items-center justify-center px-6 py-4 bg-white text-red-600 rounded-xl text-lg font-semibold hover:bg-red-50 transition-colors shadow-lg">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Cerrar Caja
                    </button>
                </div>
            </div>
            @else
            <!-- Open Cash Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-8">
                <div class="p-8 md:p-12">
                    <div class="text-center mb-10">
                        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900">No hay caja abierta</h2>
                        <p class="mt-3 text-lg text-gray-600">Abre una caja para comenzar a operar el punto de venta</p>
                    </div>

                    @if($cashRegisters->isNotEmpty())
                    <form action="{{ route('admin.cash.open') }}" method="POST" class="max-w-xl mx-auto">
                        @csrf
                        
                        <div class="space-y-6">
                            <!-- Cash Register Select -->
                            <div>
                                <label for="cash_register_id" class="block text-base font-semibold text-gray-900 mb-3">
                                    Selecciona la Caja Registradora
                                </label>
                                <select name="cash_register_id" id="cash_register_id" required
                                        class="block w-full px-5 py-4 text-lg border-2 border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50 hover:bg-white transition-colors">
                                    <option value="">-- Seleccionar caja --</option>
                                    @foreach($cashRegisters as $register)
                                    <option value="{{ $register->id }}">{{ $register->name }} ({{ $register->code }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Opening Amount -->
                            <div>
                                <label for="opening_amount" class="block text-base font-semibold text-gray-900 mb-3">
                                    Monto Inicial de Caja
                                </label>
                                <div class="relative">
                                    <span class="absolute left-5 top-1/2 -translate-y-1/2 text-2xl font-bold text-gray-400">$</span>
                                    <input type="number" 
                                           name="opening_amount" 
                                           id="opening_amount"
                                           step="100" 
                                           min="0"
                                           placeholder="0"
                                           required
                                           class="block w-full pl-12 pr-5 py-4 text-2xl font-bold border-2 border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50 hover:bg-white transition-colors">
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Ingresa el efectivo con el que inicias la caja</p>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-base font-semibold text-gray-900 mb-3">
                                    Notas de Apertura <span class="text-gray-400 font-normal">(opcional)</span>
                                </label>
                                <textarea name="notes" 
                                          id="notes"
                                          rows="3"
                                          placeholder="Observaciones adicionales..."
                                          class="block w-full px-5 py-4 text-base border-2 border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50 hover:bg-white transition-colors resize-none"></textarea>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center px-8 py-5 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-xl font-bold rounded-xl hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 shadow-lg transition-all transform hover:scale-[1.02]">
                                <svg class="w-7 h-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                                </svg>
                                Abrir Caja
                            </button>
                        </div>
                    </form>
                    @else
                    <div class="max-w-md mx-auto text-center">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                            <svg class="w-12 h-12 text-yellow-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <p class="text-lg font-semibold text-yellow-800">No hay cajas registradoras</p>
                            <p class="mt-2 text-yellow-700">Contacta al administrador para configurar una caja registradora.</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Cash Registers Grid -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 md:p-8 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Cajas Registradoras</h3>
                    <span class="text-sm text-gray-500">{{ $cashRegisters->count() }} caja(s)</span>
                </div>
                
                @if($cashRegisters->isEmpty())
                <div class="text-center py-8">
                    <p class="text-gray-500">No hay cajas registradoras configuradas</p>
                </div>
                @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($cashRegisters as $register)
                    <div class="border-2 border-gray-100 rounded-xl p-5 hover:border-blue-200 hover:shadow-md transition-all">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="text-lg font-bold text-gray-900">{{ $register->name }}</h4>
                                <p class="text-sm text-gray-500 mt-1">Código: {{ $register->code }}</p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $register->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                <span class="w-2 h-2 rounded-full mr-2 {{ $register->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $register->is_active ? 'Activa' : 'Inactiva' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Sessions History -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6 md:p-8 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-900">Últimas Sesiones</h3>
                        <a href="{{ route('admin.cash.sessions') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                            Ver todas →
                        </a>
                    </div>
                </div>
                
                @if($sessions->isEmpty())
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="text-lg font-medium text-gray-500">No hay sesiones registradas</p>
                    <p class="text-gray-400 mt-1">Las sesiones de caja aparecerán aquí</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Caja</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Cajero</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Apertura</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Cierre</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Monto Inicial</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Monto Final</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($sessions as $session)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-semibold text-gray-900">{{ $session->cashRegister->name ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-gray-700">{{ $session->user->name ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <p class="font-medium text-gray-900">{{ $session->opened_at->format('d/m/Y') }}</p>
                                        <p class="text-gray-500">{{ $session->opened_at->format('H:i') }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($session->closed_at)
                                    <div class="text-sm">
                                        <p class="font-medium text-gray-900">{{ $session->closed_at->format('d/m/Y') }}</p>
                                        <p class="text-gray-500">{{ $session->closed_at->format('H:i') }}</p>
                                    </div>
                                    @else
                                    <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span class="font-semibold text-gray-900">${{ number_format($session->opening_amount, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    @if($session->closing_amount)
                                    <span class="font-semibold text-gray-900">${{ number_format($session->closing_amount, 0, ',', '.') }}</span>
                                    @else
                                    <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($session->closed_at)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                        Cerrada
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                                        Abierta
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('admin.cash.sessions.show', $session) }}" 
                                           class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Ver
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($sessions->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $sessions->links() }}
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>

    @if($currentSession)
    <!-- Modal para cerrar caja -->
    <div id="closeModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 transition-opacity">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl">
                <!-- Header -->
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Cerrar Caja</h3>
                            <p class="text-sm text-gray-500">{{ $currentSession->cashRegister->name }}</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeCloseModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form action="{{ route('admin.cash.close', $currentSession) }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-6">
                        <!-- Summary -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Monto inicial</p>
                                    <p class="text-lg font-bold text-gray-900">${{ number_format($currentSession->opening_amount, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Tiempo activo</p>
                                    <p class="text-lg font-bold text-gray-900">{{ $currentSession->opened_at->diffForHumans(null, true) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Closing Amount -->
                        <div>
                            <label for="closing_amount" class="block text-base font-semibold text-gray-900 mb-3">
                                Monto de Cierre
                            </label>
                            <div class="relative">
                                <span class="absolute left-5 top-1/2 -translate-y-1/2 text-2xl font-bold text-gray-400">$</span>
                                <input type="number" 
                                       name="closing_amount" 
                                       id="closing_amount"
                                       step="100" 
                                       min="0"
                                       placeholder="0"
                                       required
                                       class="block w-full pl-12 pr-5 py-4 text-2xl font-bold border-2 border-gray-200 rounded-xl shadow-sm focus:border-red-500 focus:ring-red-500 bg-gray-50 hover:bg-white transition-colors">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Cuenta el efectivo físico en caja</p>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="close_notes" class="block text-base font-semibold text-gray-900 mb-3">
                                Notas de Cierre <span class="text-gray-400 font-normal">(opcional)</span>
                            </label>
                            <textarea name="notes" 
                                      id="close_notes"
                                      rows="3"
                                      placeholder="Observaciones del cierre..."
                                      class="block w-full px-5 py-4 text-base border-2 border-gray-200 rounded-xl shadow-sm focus:border-red-500 focus:ring-red-500 bg-gray-50 hover:bg-white transition-colors resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="p-6 border-t border-gray-100 flex gap-4">
                        <button type="button" onclick="closeCloseModal()" 
                                class="flex-1 px-6 py-4 border-2 border-gray-200 text-gray-700 rounded-xl text-lg font-semibold hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="flex-1 px-6 py-4 bg-red-600 text-white rounded-xl text-lg font-semibold hover:bg-red-700 transition-colors shadow-lg">
                            Cerrar Caja
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCloseModal() {
            document.getElementById('closeModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeCloseModal() {
            document.getElementById('closeModal').classList.add('hidden');
            document.body.style.overflow = '';
        }
        
        // Cerrar modal al hacer clic fuera
        document.getElementById('closeModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCloseModal();
            }
        });

        // Cerrar con Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCloseModal();
            }
        });
    </script>
    @endif
</x-layouts.app>
