<x-layouts.app title="Configuración General">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Configuración General</h1>
                <p class="text-sm text-gray-500">Configura los ajustes generales del sistema</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl">
        <x-card>
            <form action="{{ route('admin.settings.general.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="space-y-6">
                    <!-- Información de la Empresa -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Información de la Empresa</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="app_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nombre del Negocio <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="app_name" 
                                    id="app_name" 
                                    value="{{ old('app_name', config('app.name')) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('app_name') border-red-500 @enderror"
                                    required
                                >
                                @error('app_name')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="app_logo" class="block text-sm font-medium text-gray-700 mb-1">
                                    Logo
                                </label>
                                <input 
                                    type="file" 
                                    name="app_logo" 
                                    id="app_logo" 
                                    accept="image/*"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('app_logo') border-red-500 @enderror"
                                >
                                @error('app_logo')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Formatos: PNG, JPG. Tamaño máximo: 2MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Formato y Localización -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Formato y Localización</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">
                                    Moneda <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="currency" 
                                    id="currency"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('currency') border-red-500 @enderror"
                                    required
                                >
                                    <option value="COP" selected>COP - Peso Colombiano</option>
                                    <option value="USD">USD - Dólar Americano</option>
                                    <option value="EUR">EUR - Euro</option>
                                </select>
                                @error('currency')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="timezone" class="block text-sm font-medium text-gray-700 mb-1">
                                    Zona Horaria <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="timezone" 
                                    id="timezone"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('timezone') border-red-500 @enderror"
                                    required
                                >
                                    <option value="America/Bogota" selected>America/Bogota</option>
                                    <option value="America/New_York">America/New_York</option>
                                    <option value="America/Mexico_City">America/Mexico_City</option>
                                    <option value="Europe/Madrid">Europe/Madrid</option>
                                </select>
                                @error('timezone')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="date_format" class="block text-sm font-medium text-gray-700 mb-1">
                                    Formato de Fecha <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="date_format" 
                                    id="date_format"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('date_format') border-red-500 @enderror"
                                    required
                                >
                                    <option value="d/m/Y" selected>DD/MM/YYYY (24/11/2025)</option>
                                    <option value="m/d/Y">MM/DD/YYYY (11/24/2025)</option>
                                    <option value="Y-m-d">YYYY-MM-DD (2025-11-24)</option>
                                </select>
                                @error('date_format')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="time_format" class="block text-sm font-medium text-gray-700 mb-1">
                                    Formato de Hora <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="time_format" 
                                    id="time_format"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('time_format') border-red-500 @enderror"
                                    required
                                >
                                    <option value="H:i" selected>24 horas (14:30)</option>
                                    <option value="h:i A">12 horas (02:30 PM)</option>
                                </select>
                                @error('time_format')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Información Fiscal (Colombia) -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Información Fiscal</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="business_nit" class="block text-sm font-medium text-gray-700 mb-1">
                                    NIT
                                </label>
                                <input 
                                    type="text" 
                                    name="business_nit" 
                                    id="business_nit" 
                                    value="{{ old('business_nit') }}"
                                    placeholder="900123456-7"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                            </div>

                            <div>
                                <label for="business_regime" class="block text-sm font-medium text-gray-700 mb-1">
                                    Régimen Tributario
                                </label>
                                <select 
                                    name="business_regime" 
                                    id="business_regime"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">Seleccionar...</option>
                                    <option value="simplified">Régimen Simplificado</option>
                                    <option value="common">Régimen Común</option>
                                    <option value="special">Régimen Especial</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="business_address" class="block text-sm font-medium text-gray-700 mb-1">
                                    Dirección
                                </label>
                                <input 
                                    type="text" 
                                    name="business_address" 
                                    id="business_address" 
                                    value="{{ old('business_address') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                            </div>

                            <div>
                                <label for="business_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                    Teléfono
                                </label>
                                <input 
                                    type="text" 
                                    name="business_phone" 
                                    id="business_phone" 
                                    value="{{ old('business_phone') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                            </div>

                            <div>
                                <label for="business_email" class="block text-sm font-medium text-gray-700 mb-1">
                                    Email
                                </label>
                                <input 
                                    type="email" 
                                    name="business_email" 
                                    id="business_email" 
                                    value="{{ old('business_email') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
