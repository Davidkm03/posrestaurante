<x-layouts.app title="Admin">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Integraciones
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Rappi Integration -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden lg:col-span-2">
                    <div class="p-4 border-b bg-gradient-to-r from-red-500 to-orange-500 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                                <span class="text-red-500 font-bold text-xl">R</span>
                            </div>
                            <div class="text-white">
                                <h3 class="font-semibold text-lg">Rappi</h3>
                                <p class="text-sm text-red-100">Integración con plataforma de delivery</p>
                            </div>
                        </div>
                        @if($rappiConnected ?? false)
                            <span class="px-3 py-1 bg-green-500 text-white text-sm font-medium rounded-full flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Conectado
                            </span>
                        @else
                            <span class="px-3 py-1 bg-gray-200 text-gray-700 text-sm font-medium rounded-full">
                                Desconectado
                            </span>
                        @endif
                    </div>

                    @if($rappiConnected ?? false)
                        <div class="p-4 bg-green-50 border-b border-green-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="text-sm">
                                        <span class="text-gray-600">Store ID:</span>
                                        <span class="font-medium text-gray-900">{{ $rappiStoreId ?? 'N/A' }}</span>
                                    </div>
                                    <div class="text-sm">
                                        <span class="text-gray-600">Dominio:</span>
                                        <span class="font-medium text-gray-900">{{ $rappiDomain ?? 'N/A' }}</span>
                                    </div>
                                    @if($rappiLastSync ?? null)
                                        <div class="text-sm">
                                            <span class="text-gray-600">Última sincronización:</span>
                                            <span class="font-medium text-gray-900">{{ $rappiLastSync }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('admin.integrations.rappi.sync') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            Sincronizar Órdenes
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.integrations.rappi.sync-menu') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Sincronizar Menú
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.integrations.rappi.disconnect') }}" class="inline" onsubmit="return confirm('¿Estás seguro de desconectar Rappi?')">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                                            Desconectar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Estadísticas Rappi -->
                        <div class="p-4 grid grid-cols-4 gap-4">
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ $rappiStats['pending'] ?? 0 }}</div>
                                <div class="text-sm text-gray-500">Pendientes</div>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold text-yellow-600">{{ $rappiStats['preparing'] ?? 0 }}</div>
                                <div class="text-sm text-gray-500">En Preparación</div>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $rappiStats['ready'] ?? 0 }}</div>
                                <div class="text-sm text-gray-500">Listas</div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $rappiStats['today'] ?? 0 }}</div>
                                <div class="text-sm text-gray-500">Hoy</div>
                            </div>
                        </div>
                    @else
                        <form method="POST" action="{{ route('admin.integrations.rappi.connect') }}">
                            @csrf
                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Client ID <span class="text-red-500">*</span></label>
                                        <input type="text" name="client_id" value="{{ old('client_id') }}" required
                                               class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"
                                               placeholder="Tu Client ID de Rappi">
                                        @error('client_id')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Client Secret <span class="text-red-500">*</span></label>
                                        <input type="password" name="client_secret" value="{{ old('client_secret') }}" required
                                               class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"
                                               placeholder="Tu Client Secret de Rappi">
                                        @error('client_secret')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Store ID <span class="text-red-500">*</span></label>
                                        <input type="text" name="store_id" value="{{ old('store_id') }}" required
                                               class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"
                                               placeholder="ID de tu tienda en Rappi">
                                        @error('store_id')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">País / Dominio <span class="text-red-500">*</span></label>
                                        <select name="domain" required class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                                            <option value="">Seleccionar país...</option>
                                            <option value="api.rappi.com.co" {{ old('domain') === 'api.rappi.com.co' ? 'selected' : '' }}>Colombia (api.rappi.com.co)</option>
                                            <option value="api.rappi.com.mx" {{ old('domain') === 'api.rappi.com.mx' ? 'selected' : '' }}>México (api.rappi.com.mx)</option>
                                            <option value="api.rappi.com.br" {{ old('domain') === 'api.rappi.com.br' ? 'selected' : '' }}>Brasil (api.rappi.com.br)</option>
                                            <option value="api.rappi.com.ar" {{ old('domain') === 'api.rappi.com.ar' ? 'selected' : '' }}>Argentina (api.rappi.com.ar)</option>
                                            <option value="api.rappi.cl" {{ old('domain') === 'api.rappi.cl' ? 'selected' : '' }}>Chile (api.rappi.cl)</option>
                                            <option value="api.rappi.pe" {{ old('domain') === 'api.rappi.pe' ? 'selected' : '' }}>Perú (api.rappi.pe)</option>
                                            <option value="api.rappi.com.ec" {{ old('domain') === 'api.rappi.com.ec' ? 'selected' : '' }}>Ecuador (api.rappi.com.ec)</option>
                                            <option value="api.rappi.com.uy" {{ old('domain') === 'api.rappi.com.uy' ? 'selected' : '' }}>Uruguay (api.rappi.com.uy)</option>
                                            <option value="api.dev.rappi.com" {{ old('domain') === 'api.dev.rappi.com' ? 'selected' : '' }}>🧪 Desarrollo (api.dev.rappi.com)</option>
                                        </select>
                                        @error('domain')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="flex items-start gap-2 p-3 bg-blue-50 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="text-sm text-blue-700">
                                        <p class="font-medium">¿Cómo obtener las credenciales?</p>
                                        <p class="mt-1">Contacta al equipo de Rappi Partners para solicitar acceso a su API. Necesitarás registrar tu restaurante en el programa de partners.</p>
                                    </div>
                                </div>
                                <div class="pt-2">
                                    <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-red-500 to-orange-500 text-white font-medium rounded-lg hover:from-red-600 hover:to-orange-600 transition-all flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                        </svg>
                                        Conectar con Rappi
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>

                <!-- Facturación Electrónica - Factus -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden lg:col-span-2">
                    <div class="p-4 border-b bg-gradient-to-r from-blue-600 to-indigo-600 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="text-white">
                                <h3 class="font-semibold text-lg">Factus - Facturación Electrónica DIAN</h3>
                                <p class="text-sm text-blue-100">Facturación electrónica Colombia</p>
                            </div>
                        </div>
                        @php
                            $factusConfigured = !empty(config('factus.client_id')) && !empty(config('factus.username'));
                            $isSandbox = str_contains(config('factus.base_url', ''), 'sandbox');
                        @endphp
                        @if($factusConfigured)
                            <span class="px-3 py-1 bg-green-500 text-white text-sm font-medium rounded-full flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Configurado
                            </span>
                        @else
                            <span class="px-3 py-1 bg-gray-200 text-gray-700 text-sm font-medium rounded-full">
                                Sin configurar
                            </span>
                        @endif
                    </div>

                    @if($factusConfigured)
                        <!-- Configuración activa -->
                        <div class="p-4 bg-green-50 border-b border-green-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-6">
                                    <div class="text-sm">
                                        <span class="text-gray-600">Ambiente:</span>
                                        <span class="font-medium {{ $isSandbox ? 'text-yellow-600' : 'text-green-600' }}">
                                            {{ $isSandbox ? '🧪 Sandbox' : '🚀 Producción' }}
                                        </span>
                                    </div>
                                    <div class="text-sm">
                                        <span class="text-gray-600">Usuario:</span>
                                        <span class="font-medium text-gray-900">{{ config('factus.username') }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('admin.integrations.factus.test') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Probar Conexión
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.integrations.factus.sync') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            Sincronizar Catálogos
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.integrations.factus.test-invoice') }}" 
                                       class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Factura de Prueba
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Instrucciones de configuración -->
                    <div class="p-6">
                        <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-lg mb-4">
                            <svg class="w-6 h-6 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium mb-2">Configuración via archivo .env</p>
                                <p>Las credenciales de Factus se configuran en el archivo <code class="bg-blue-100 px-1 rounded">.env</code> del proyecto:</p>
                                <pre class="mt-2 p-3 bg-gray-800 text-green-400 rounded-lg text-xs overflow-x-auto">
# Factus API Configuration
FACTUS_BASE_URL=https://api-sandbox.factus.com.co
FACTUS_CLIENT_ID=tu_client_id
FACTUS_CLIENT_SECRET=tu_client_secret
FACTUS_USERNAME=tu_email@ejemplo.com
FACTUS_PASSWORD=tu_contraseña</pre>
                                <p class="mt-2">Para producción, cambia <code class="bg-blue-100 px-1 rounded">FACTUS_BASE_URL</code> a <code class="bg-blue-100 px-1 rounded">https://api.factus.com.co</code></p>
                            </div>
                        </div>

                        <!-- Token Storage Info -->
                        <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                            <svg class="w-6 h-6 text-gray-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                            <div class="text-sm text-gray-600">
                                <p class="font-medium mb-1">Token de Acceso</p>
                                <p>El token de autenticación se guarda automáticamente en <code class="bg-gray-200 px-1 rounded">storage/app/factus/token.json</code> y se refresca automáticamente cuando expira.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagos -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b bg-gray-50 flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Pasarelas de Pago</h3>
                            <p class="text-sm text-gray-500">Configurar métodos de pago electrónico</p>
                        </div>
                    </div>
                    <div class="p-4 space-y-3">
                        <!-- Wompi -->
                        <div class="p-3 border rounded-lg">
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-medium">Wompi</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="wompi_enabled" class="sr-only peer" {{ config('integrations.wompi_enabled') ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            <input type="password" placeholder="Llave pública" class="w-full border-gray-300 rounded-lg text-sm mb-2">
                            <input type="password" placeholder="Llave privada" class="w-full border-gray-300 rounded-lg text-sm">
                        </div>

                        <!-- Nequi -->
                        <div class="p-3 border rounded-lg">
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-medium">Nequi</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="nequi_enabled" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            <input type="text" placeholder="API Key" class="w-full border-gray-300 rounded-lg text-sm">
                        </div>

                        <!-- Daviplata -->
                        <div class="p-3 border rounded-lg">
                            <div class="flex items-center justify-between">
                                <span class="font-medium">Daviplata</span>
                                <span class="text-xs text-gray-500">Próximamente</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Otras Plataformas de Delivery -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b bg-gray-50 flex items-center gap-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Otras Plataformas de Delivery</h3>
                            <p class="text-sm text-gray-500">Más integraciones próximamente</p>
                        </div>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="p-3 border rounded-lg flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center text-white font-bold text-sm">D</div>
                                <span class="font-medium">Didi Food</span>
                            </div>
                            <span class="text-xs text-gray-500">Próximamente</span>
                        </div>
                        <div class="p-3 border rounded-lg flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center text-white font-bold text-sm">U</div>
                                <span class="font-medium">Uber Eats</span>
                            </div>
                            <span class="text-xs text-gray-500">Próximamente</span>
                        </div>
                        <div class="p-3 border rounded-lg flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-pink-500 rounded-lg flex items-center justify-center text-white font-bold text-sm">iF</div>
                                <span class="font-medium">iFood</span>
                            </div>
                            <span class="text-xs text-gray-500">Próximamente</span>
                        </div>
                    </div>
                </div>

                <!-- Contabilidad -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b bg-gray-50 flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Software Contable</h3>
                            <p class="text-sm text-gray-500">Exportar a sistemas contables</p>
                        </div>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="p-3 border rounded-lg flex items-center justify-between">
                            <span class="font-medium">Siigo</span>
                            <span class="text-xs text-gray-500">Próximamente</span>
                        </div>
                        <div class="p-3 border rounded-lg flex items-center justify-between">
                            <span class="font-medium">Alegra</span>
                            <span class="text-xs text-gray-500">Próximamente</span>
                        </div>
                        <div class="p-3 border rounded-lg flex items-center justify-between">
                            <span class="font-medium">World Office</span>
                            <span class="text-xs text-gray-500">Próximamente</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
