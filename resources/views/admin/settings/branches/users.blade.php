@extends('layouts.admin')

@section('title', 'Usuarios - ' . $branch->name)

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
                <h1 class="text-2xl font-bold text-gray-900">Usuarios de la Sucursal</h1>
                <p class="text-gray-600">{{ $branch->name }}</p>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-gray-200">
        <nav class="flex gap-4">
            <a href="{{ route('admin.settings.branches.settings', $branch) }}" class="px-4 py-2 text-gray-500 hover:text-gray-700">
                General
            </a>
            <a href="{{ route('admin.settings.branches.hours', $branch) }}" class="px-4 py-2 text-gray-500 hover:text-gray-700">
                Horarios
            </a>
            <a href="{{ route('admin.settings.branches.users', $branch) }}" class="px-4 py-2 text-blue-600 border-b-2 border-blue-600 font-medium">
                Usuarios
            </a>
        </nav>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Usuarios Asignados --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                Usuarios Asignados
                <span class="text-sm font-normal text-gray-500">({{ $branchUsers->count() }})</span>
            </h3>

            @if($branchUsers->count() > 0)
                <div class="space-y-3">
                    @foreach($branchUsers as $user)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-medium">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($user->pivot->is_default)
                                    <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">Principal</span>
                                @endif
                                <form action="{{ route('admin.settings.branches.remove-user', [$branch, $user]) }}" method="POST" onsubmit="return confirm('¿Remover este usuario de la sucursal?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p>No hay usuarios asignados</p>
                </div>
            @endif
        </div>

        {{-- Agregar Usuario --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Agregar Usuario</h3>

            @if($availableUsers->count() > 0)
                <form action="{{ route('admin.settings.branches.add-user', $branch) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Seleccionar Usuario</label>
                        <select name="user_id" id="user_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Seleccionar --</option>
                            @foreach($availableUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Agregar a Sucursal
                    </button>
                </form>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>Todos los usuarios ya están asignados</p>
                    <a href="{{ route('admin.users.create') }}" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">
                        Crear nuevo usuario →
                    </a>
                </div>
            @endif

            <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div class="text-sm text-yellow-700">
                        <p class="font-medium">Nota</p>
                        <p>Los usuarios asignados podrán acceder a esta sucursal y ver sus datos según sus permisos.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
