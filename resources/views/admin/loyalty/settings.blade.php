@extends('layouts.admin')

@section('title', 'Configuración de Lealtad')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.loyalty.index') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Configuración de Lealtad</h1>
                <p class="text-gray-600">Configura los parámetros del programa de puntos</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.loyalty.update-settings') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Configuración de Puntos --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Acumulación de Puntos</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Puntos por cada compra</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="points_per_amount" value="{{ $settings['points_per_amount'] }}" min="1" required class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <span class="text-gray-600">punto(s) por cada</span>
                            <input type="number" name="amount_for_points" value="{{ $settings['amount_for_points'] }}" min="100" step="100" required class="w-28 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <span class="text-gray-600">pesos gastados</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Ejemplo: 1 punto por cada $1,000 gastados</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Valor de cada punto al canjear</label>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-600">$</span>
                            <input type="number" name="point_value" value="{{ $settings['point_value'] }}" min="1" required class="w-28 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <span class="text-gray-600">por punto</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Cuánto descuento obtiene el cliente por cada punto</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mínimo de puntos para canjear</label>
                        <input type="number" name="min_points_redeem" value="{{ $settings['min_points_redeem'] }}" min="1" required class="w-28 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">El cliente debe tener al menos esta cantidad para redimir</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Días para expiración</label>
                        <input type="number" name="expiration_days" value="{{ $settings['expiration_days'] }}" min="0" required class="w-28 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">0 = Los puntos nunca expiran</p>
                    </div>
                </div>
            </div>

            {{-- Bonos --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Bonificaciones</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bonus de cumpleaños</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="birthday_bonus" value="{{ $settings['birthday_bonus'] }}" min="0" class="w-28 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <span class="text-gray-600">puntos</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Puntos de regalo en el cumpleaños (niveles Silver+)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Puntos por referido</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="referral_points" value="{{ $settings['referral_points'] }}" min="0" class="w-28 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <span class="text-gray-600">puntos</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Puntos que recibe un cliente cuando refiere a alguien</p>
                    </div>
                </div>

                {{-- Ejemplo de cálculo --}}
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <h4 class="font-medium text-blue-900 mb-2">Ejemplo de cálculo</h4>
                    <p class="text-sm text-blue-700">
                        Con la configuración actual:<br>
                        • Un cliente que gaste <strong>$50,000</strong> acumula <strong>{{ number_format(($settings['points_per_amount'] / $settings['amount_for_points']) * 50000) }} puntos</strong><br>
                        • Esos puntos equivalen a <strong>${{ number_format((($settings['points_per_amount'] / $settings['amount_for_points']) * 50000) * $settings['point_value']) }}</strong> en descuentos
                    </p>
                </div>
            </div>
        </div>

        {{-- Niveles --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Niveles del Programa</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($levels as $level => $config)
                    <div class="border rounded-lg p-4" style="border-color: {{ $config['color'] }}">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color: {{ $config['color'] }}20">
                                <svg class="w-5 h-5" style="color: {{ $config['color'] }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ ucfirst($level) }}</h4>
                                <p class="text-sm text-gray-500">{{ number_format($config['min_points']) }}+ pts</p>
                            </div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Multiplicador:</span>
                                <span class="font-medium text-gray-900">{{ $config['multiplier'] }}x</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Beneficios:</span>
                                <ul class="mt-1 space-y-1">
                                    @forelse($config['benefits'] as $benefit)
                                        <li class="flex items-center gap-1 text-gray-600">
                                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            @switch($benefit)
                                                @case('birthday_bonus')
                                                    Bonus cumpleaños
                                                    @break
                                                @case('priority_reservation')
                                                    Reservas prioritarias
                                                    @break
                                                @case('exclusive_events')
                                                    Eventos exclusivos
                                                    @break
                                                @default
                                                    {{ $benefit }}
                                            @endswitch
                                        </li>
                                    @empty
                                        <li class="text-gray-400">Sin beneficios adicionales</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 p-4 bg-yellow-50 rounded-lg">
                <p class="text-sm text-yellow-700">
                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Los niveles se configuran en el archivo <code class="bg-yellow-100 px-1 rounded">config/loyalty.php</code>
                </p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('admin.loyalty.index') }}" class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Guardar Configuración
            </button>
        </div>
    </form>
</div>
@endsection
