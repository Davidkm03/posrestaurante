@extends('layouts.admin')

@section('title', 'Programa de Lealtad')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Programa de Lealtad</h1>
            <p class="text-gray-600">Gestión de puntos y niveles de clientes</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.loyalty.settings') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Configuración
            </a>
            <a href="{{ route('admin.loyalty.export') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Exportar
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Clientes con Puntos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_customers']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Puntos Activos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_points_active']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Ganados (Este mes)</p>
                    <p class="text-2xl font-bold text-green-600">+{{ number_format($stats['points_earned_month']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Canjeados (Este mes)</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['points_redeemed_month']) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Distribución por Niveles --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribución por Niveles</h3>
            <div class="space-y-4">
                @foreach($levelDistribution as $level => $data)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-4 h-4 rounded-full" style="background-color: {{ $data['color'] }}"></div>
                            <span class="font-medium text-gray-700">{{ $data['name'] }}</span>
                        </div>
                        <span class="text-lg font-bold text-gray-900">{{ $data['count'] }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Chart placeholder --}}
            <div class="mt-6 pt-6 border-t">
                <canvas id="levelChart" height="200"></canvas>
            </div>
        </div>

        {{-- Top Clientes --}}
        <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-2">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 10 Clientes</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-gray-500 border-b">
                            <th class="pb-3 font-medium">#</th>
                            <th class="pb-3 font-medium">Cliente</th>
                            <th class="pb-3 font-medium">Nivel</th>
                            <th class="pb-3 font-medium text-right">Puntos</th>
                            <th class="pb-3 font-medium text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($topCustomers as $index => $customer)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 text-gray-500">{{ $index + 1 }}</td>
                                <td class="py-3">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $customer->full_name }}</p>
                                        <p class="text-sm text-gray-500">{{ $customer->email }}</p>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium" 
                                          style="background-color: {{ config('loyalty.levels.' . $customer->level . '.color', '#CD7F32') }}20; color: {{ config('loyalty.levels.' . $customer->level . '.color', '#CD7F32') }}">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ ucfirst($customer->level) }}
                                    </span>
                                </td>
                                <td class="py-3 text-right">
                                    <span class="font-bold text-gray-900">{{ number_format($customer->loyalty_points) }}</span>
                                </td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('admin.loyalty.customer-history', $customer) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Ver historial
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-500">
                                    No hay clientes con puntos aún
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Actividad Reciente --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Actividad Reciente</h3>
            <form action="{{ route('admin.loyalty.expire-points') }}" method="POST" class="inline" onsubmit="return confirm('¿Expirar puntos vencidos?')">
                @csrf
                <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">
                    Expirar puntos vencidos
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-gray-500 border-b">
                        <th class="pb-3 font-medium">Fecha</th>
                        <th class="pb-3 font-medium">Cliente</th>
                        <th class="pb-3 font-medium">Tipo</th>
                        <th class="pb-3 font-medium">Descripción</th>
                        <th class="pb-3 font-medium text-right">Puntos</th>
                        <th class="pb-3 font-medium text-right">Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($recentActivity as $activity)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 text-sm text-gray-500">
                                {{ $activity->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3">
                                @if($activity->customer)
                                    <a href="{{ route('admin.loyalty.customer-history', $activity->customer) }}" class="font-medium text-blue-600 hover:text-blue-800">
                                        {{ $activity->customer->full_name }}
                                    </a>
                                @else
                                    <span class="text-gray-400">Cliente eliminado</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-{{ $activity->getTypeColor() }}-100 text-{{ $activity->getTypeColor() }}-800">
                                    {{ $activity->getTypeLabel() }}
                                </span>
                            </td>
                            <td class="py-3 text-sm text-gray-600">
                                {{ $activity->description }}
                            </td>
                            <td class="py-3 text-right font-medium {{ $activity->points > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $activity->points > 0 ? '+' : '' }}{{ number_format($activity->points) }}
                            </td>
                            <td class="py-3 text-right text-gray-900">
                                {{ number_format($activity->balance_after) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">
                                No hay actividad registrada
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const levelData = @json($levelDistribution);
    const labels = Object.keys(levelData).map(l => levelData[l].name);
    const values = Object.keys(levelData).map(l => levelData[l].count);
    const colors = Object.keys(levelData).map(l => levelData[l].color);

    new Chart(document.getElementById('levelChart'), {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                }
            }
        }
    });
</script>
@endpush
