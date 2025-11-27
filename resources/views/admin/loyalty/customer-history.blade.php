@extends('layouts.admin')

@section('title', 'Historial de Lealtad - ' . $customer->full_name)

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
                <h1 class="text-2xl font-bold text-gray-900">{{ $customer->full_name }}</h1>
                <p class="text-gray-600">{{ $customer->email }} • {{ $customer->phone }}</p>
            </div>
        </div>
        <button onclick="document.getElementById('adjustModal').classList.remove('hidden')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Ajustar Puntos
        </button>
    </div>

    {{-- Customer Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Nivel Actual --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Nivel Actual</p>
                    <p class="text-2xl font-bold" style="color: {{ $levelConfig['color'] ?? '#CD7F32' }}">
                        {{ ucfirst($level) }}
                    </p>
                </div>
                <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background-color: {{ $levelConfig['color'] ?? '#CD7F32' }}20">
                    <svg class="w-8 h-8" style="color: {{ $levelConfig['color'] ?? '#CD7F32' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-2">Multiplicador: {{ $levelConfig['multiplier'] ?? 1 }}x</p>
        </div>

        {{-- Puntos Actuales --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Puntos Actuales</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($customer->loyalty_points) }}</p>
                </div>
            </div>
        </div>

        {{-- Total Ganado --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Ganados</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['total_earned']) }}</p>
                </div>
            </div>
        </div>

        {{-- Total Canjeado --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Canjeados</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_redeemed']) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Additional Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h4 class="font-medium text-gray-700 mb-4">Resumen</h4>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Bonus recibidos:</dt>
                    <dd class="font-medium text-gray-900">{{ number_format($stats['total_bonus']) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Puntos expirados:</dt>
                    <dd class="font-medium text-red-600">{{ number_format($stats['total_expired']) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Transacciones:</dt>
                    <dd class="font-medium text-gray-900">{{ $stats['total_transactions'] }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Promedio mensual:</dt>
                    <dd class="font-medium text-gray-900">{{ number_format($stats['monthly_average']) }} pts</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h4 class="font-medium text-gray-700 mb-4">Actividad</h4>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Primera actividad:</dt>
                    <dd class="font-medium text-gray-900">
                        {{ $stats['first_activity'] ? $stats['first_activity']->format('d/m/Y') : 'N/A' }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Última actividad:</dt>
                    <dd class="font-medium text-gray-900">
                        {{ $stats['last_activity'] ? $stats['last_activity']->format('d/m/Y') : 'N/A' }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Cliente desde:</dt>
                    <dd class="font-medium text-gray-900">{{ $customer->created_at->format('d/m/Y') }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h4 class="font-medium text-gray-700 mb-4">Beneficios del Nivel</h4>
            @if(!empty($levelConfig['benefits']))
                <ul class="space-y-2">
                    @foreach($levelConfig['benefits'] as $benefit)
                        <li class="flex items-center gap-2 text-gray-600">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            @switch($benefit)
                                @case('birthday_bonus')
                                    Bonus de cumpleaños
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
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">Sin beneficios adicionales</p>
            @endif
        </div>
    </div>

    {{-- Historial --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Historial de Puntos</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-gray-500 border-b">
                        <th class="pb-3 font-medium">Fecha</th>
                        <th class="pb-3 font-medium">Tipo</th>
                        <th class="pb-3 font-medium">Descripción</th>
                        <th class="pb-3 font-medium">Orden</th>
                        <th class="pb-3 font-medium text-right">Puntos</th>
                        <th class="pb-3 font-medium text-right">Balance</th>
                        <th class="pb-3 font-medium">Expira</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($history as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 text-sm text-gray-500">
                                {{ $record->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-{{ $record->getTypeColor() }}-100 text-{{ $record->getTypeColor() }}-800">
                                    {{ $record->getTypeLabel() }}
                                </span>
                            </td>
                            <td class="py-3 text-sm text-gray-600">
                                {{ $record->description }}
                            </td>
                            <td class="py-3 text-sm">
                                @if($record->order)
                                    <a href="{{ route('admin.orders.show', $record->order) }}" class="text-blue-600 hover:text-blue-800">
                                        #{{ $record->order->order_number }}
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="py-3 text-right font-medium {{ $record->points > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $record->points > 0 ? '+' : '' }}{{ number_format($record->points) }}
                            </td>
                            <td class="py-3 text-right text-gray-900">
                                {{ number_format($record->balance_after) }}
                            </td>
                            <td class="py-3 text-sm">
                                @if($record->expires_at)
                                    <span class="{{ $record->expires_at->isPast() ? 'text-red-500' : 'text-gray-500' }}">
                                        {{ $record->expires_at->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                No hay historial de puntos
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $history->links() }}
        </div>
    </div>
</div>

{{-- Modal Ajustar Puntos --}}
<div id="adjustModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Ajustar Puntos</h3>
                <button onclick="document.getElementById('adjustModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.loyalty.adjust-points', $customer) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Puntos</label>
                        <input type="number" name="points" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ej: 50 o -50">
                        <p class="text-xs text-gray-500 mt-1">Use valores negativos para restar puntos</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Razón</label>
                        <input type="text" name="reason" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ej: Compensación por mal servicio">
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('adjustModal').classList.add('hidden')" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Guardar Ajuste
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
