@extends('layouts.admin')

@section('title', 'Configuración - ' . $branch->name)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.settings.branches') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Configuración de Sucursal</h1>
                <p class="text-gray-600">{{ $branch->name }}</p>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-gray-200">
        <nav class="flex gap-4">
            <a href="{{ route('admin.settings.branches.settings', $branch) }}" class="px-4 py-2 text-blue-600 border-b-2 border-blue-600 font-medium">
                General
            </a>
            <a href="{{ route('admin.settings.branches.hours', $branch) }}" class="px-4 py-2 text-gray-500 hover:text-gray-700">
                Horarios
            </a>
            <a href="{{ route('admin.settings.branches.users', $branch) }}" class="px-4 py-2 text-gray-500 hover:text-gray-700">
                Usuarios
            </a>
        </nav>
    </div>

    <form action="{{ route('admin.settings.branches.update-settings', $branch) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Regional --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Configuración Regional</h3>
                
                <div class="space-y-4">
                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700 mb-1">Zona Horaria</label>
                        <select name="timezone" id="timezone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="America/Bogota" {{ ($branch->settings['timezone'] ?? 'America/Bogota') == 'America/Bogota' ? 'selected' : '' }}>America/Bogota</option>
                            <option value="America/Mexico_City" {{ ($branch->settings['timezone'] ?? '') == 'America/Mexico_City' ? 'selected' : '' }}>America/Mexico_City</option>
                            <option value="America/Lima" {{ ($branch->settings['timezone'] ?? '') == 'America/Lima' ? 'selected' : '' }}>America/Lima</option>
                            <option value="America/Santiago" {{ ($branch->settings['timezone'] ?? '') == 'America/Santiago' ? 'selected' : '' }}>America/Santiago</option>
                            <option value="America/Buenos_Aires" {{ ($branch->settings['timezone'] ?? '') == 'America/Buenos_Aires' ? 'selected' : '' }}>America/Buenos_Aires</option>
                        </select>
                    </div>

                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Moneda</label>
                        <select name="currency" id="currency" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="COP" {{ ($branch->settings['currency'] ?? 'COP') == 'COP' ? 'selected' : '' }}>COP - Peso Colombiano</option>
                            <option value="USD" {{ ($branch->settings['currency'] ?? '') == 'USD' ? 'selected' : '' }}>USD - Dólar Estadounidense</option>
                            <option value="MXN" {{ ($branch->settings['currency'] ?? '') == 'MXN' ? 'selected' : '' }}>MXN - Peso Mexicano</option>
                            <option value="PEN" {{ ($branch->settings['currency'] ?? '') == 'PEN' ? 'selected' : '' }}>PEN - Sol Peruano</option>
                            <option value="ARS" {{ ($branch->settings['currency'] ?? '') == 'ARS' ? 'selected' : '' }}>ARS - Peso Argentino</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Impuestos y Cargos --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Impuestos y Cargos</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="tax_included" value="1" 
                                   {{ ($branch->settings['tax_included'] ?? true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm font-medium text-gray-700">Impuestos incluidos en precios</span>
                        </label>
                    </div>

                    <div>
                        <label for="default_tax_rate" class="block text-sm font-medium text-gray-700 mb-1">Tasa de IVA por defecto (%)</label>
                        <input type="number" name="default_tax_rate" id="default_tax_rate" min="0" max="100" step="0.01"
                               value="{{ $branch->settings['default_tax_rate'] ?? 19 }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="service_charge" class="block text-sm font-medium text-gray-700 mb-1">Cargo por servicio (%)</label>
                        <input type="number" name="service_charge" id="service_charge" min="0" max="100" step="0.01"
                               value="{{ $branch->settings['service_charge'] ?? 0 }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Deja en 0 si no aplica cargo por servicio</p>
                    </div>
                </div>
            </div>

            {{-- Recibos --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Personalización de Recibos</h3>
                
                <div class="space-y-4">
                    <div>
                        <label for="receipt_header" class="block text-sm font-medium text-gray-700 mb-1">Encabezado del recibo</label>
                        <textarea name="receipt_header" id="receipt_header" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Texto que aparece en la parte superior del recibo">{{ $branch->settings['receipt_header'] ?? '' }}</textarea>
                    </div>

                    <div>
                        <label for="receipt_footer" class="block text-sm font-medium text-gray-700 mb-1">Pie del recibo</label>
                        <textarea name="receipt_footer" id="receipt_footer" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Texto que aparece en la parte inferior del recibo">{{ $branch->settings['receipt_footer'] ?? 'Gracias por su visita' }}</textarea>
                    </div>

                    <div>
                        <label for="invoice_notes" class="block text-sm font-medium text-gray-700 mb-1">Notas para facturas</label>
                        <textarea name="invoice_notes" id="invoice_notes" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Notas legales o información adicional para facturas">{{ $branch->settings['invoice_notes'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Operación --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Configuración Operativa</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="kitchen_display" value="1" 
                                   {{ ($branch->settings['kitchen_display'] ?? true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm font-medium text-gray-700">Habilitar pantalla de cocina</span>
                        </label>
                        <p class="text-xs text-gray-500 ml-6">Mostrar órdenes en pantalla de cocina</p>
                    </div>

                    <div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="require_table" value="1" 
                                   {{ ($branch->settings['require_table'] ?? false) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm font-medium text-gray-700">Requerir mesa para órdenes</span>
                        </label>
                        <p class="text-xs text-gray-500 ml-6">Las órdenes deben asociarse a una mesa</p>
                    </div>

                    <div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="auto_print_receipt" value="1" 
                                   {{ ($branch->settings['auto_print_receipt'] ?? false) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm font-medium text-gray-700">Imprimir recibo automáticamente</span>
                        </label>
                        <p class="text-xs text-gray-500 ml-6">Al completar el pago</p>
                    </div>

                    <div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="auto_print_kitchen" value="1" 
                                   {{ ($branch->settings['auto_print_kitchen'] ?? true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm font-medium text-gray-700">Imprimir comanda automáticamente</span>
                        </label>
                        <p class="text-xs text-gray-500 ml-6">Al confirmar la orden</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.settings.branches') }}" class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Guardar Configuración
            </button>
        </div>
    </form>
</div>
@endsection
