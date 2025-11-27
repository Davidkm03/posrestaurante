@extends('layouts.admin')

@section('title', $branch->name)

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
            <div class="flex items-center gap-4">
                @if($branch->logo)
                    <img src="{{ Storage::url($branch->logo) }}" alt="{{ $branch->name }}" class="w-16 h-16 rounded-lg object-cover">
                @else
                    <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $branch->name }}</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-sm text-gray-500">{{ $branch->code }}</span>
                        @if($branch->is_main)
                            <span class="px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Principal</span>
                        @endif
                        <span class="px-2 py-0.5 text-xs font-medium {{ $branch->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-full">
                            {{ $branch->is_active ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.settings.branches.settings', $branch) }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Configuración
            </a>
            <a href="{{ route('admin.settings.branches.edit', $branch) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </a>
        </div>
    </div>

    {{-- Stats de Hoy --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Órdenes Hoy</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_orders_today']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Ventas Hoy</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['revenue_today'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Mesas Ocupadas</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active_tables'] }}/{{ $stats['total_tables'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Usuarios</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $branch->users->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Información General --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información General</h3>
            <dl class="space-y-3">
                @if($branch->nit)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">NIT:</dt>
                        <dd class="font-medium text-gray-900">{{ $branch->nit }}</dd>
                    </div>
                @endif
                @if($branch->address)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Dirección:</dt>
                        <dd class="font-medium text-gray-900">{{ $branch->address }}</dd>
                    </div>
                @endif
                @if($branch->city)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Ciudad:</dt>
                        <dd class="font-medium text-gray-900">{{ $branch->city }}, {{ $branch->department }}</dd>
                    </div>
                @endif
                @if($branch->phone)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Teléfono:</dt>
                        <dd class="font-medium text-gray-900">{{ $branch->phone }}</dd>
                    </div>
                @endif
                @if($branch->email)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Email:</dt>
                        <dd class="font-medium text-gray-900">{{ $branch->email }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Usuarios Asignados --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Usuarios Asignados</h3>
                <a href="{{ route('admin.settings.branches.users', $branch) }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Gestionar →
                </a>
            </div>
            <div class="space-y-3">
                @forelse($branch->users->take(5) as $user)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-sm text-blue-600 font-medium">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No hay usuarios asignados</p>
                @endforelse
                @if($branch->users->count() > 5)
                    <p class="text-sm text-gray-500">+{{ $branch->users->count() - 5 }} más</p>
                @endif
            </div>
        </div>

        {{-- Zonas y Mesas --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Zonas y Mesas</h3>
                <a href="{{ route('admin.zones.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Gestionar →
                </a>
            </div>
            <div class="space-y-3">
                @forelse($branch->zones as $zone)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full" style="background-color: {{ $zone->color ?? '#6B7280' }}"></div>
                            <span class="font-medium text-gray-900">{{ $zone->name }}</span>
                        </div>
                        <span class="text-sm text-gray-500">{{ $zone->tables->count() }} mesas</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No hay zonas configuradas</p>
                @endforelse
            </div>
        </div>

        {{-- Resoluciones DIAN --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Resoluciones DIAN</h3>
                <a href="{{ route('admin.settings.resolutions') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Gestionar →
                </a>
            </div>
            <div class="space-y-3">
                @forelse($branch->dianResolutions as $resolution)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $resolution->resolution_number }}</p>
                            <p class="text-xs text-gray-500">{{ $resolution->prefix }} • Vigente hasta {{ $resolution->valid_to->format('d/m/Y') }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium {{ $resolution->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} rounded-full">
                            {{ $resolution->is_active ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No hay resoluciones configuradas</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
