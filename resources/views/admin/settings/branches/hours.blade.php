@extends('layouts.admin')

@section('title', 'Horarios - ' . $branch->name)

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
                <h1 class="text-2xl font-bold text-gray-900">Horarios de Apertura</h1>
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
            <a href="{{ route('admin.settings.branches.hours', $branch) }}" class="px-4 py-2 text-blue-600 border-b-2 border-blue-600 font-medium">
                Horarios
            </a>
            <a href="{{ route('admin.settings.branches.users', $branch) }}" class="px-4 py-2 text-gray-500 hover:text-gray-700">
                Usuarios
            </a>
        </nav>
    </div>

    @php
        $days = [
            'monday' => 'Lunes',
            'tuesday' => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday' => 'Jueves',
            'friday' => 'Viernes',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo',
        ];
        $hours = $branch->settings['opening_hours'] ?? [];
    @endphp

    <form action="{{ route('admin.settings.branches.update-hours', $branch) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="space-y-4">
                @foreach($days as $key => $dayName)
                    @php
                        $dayHours = $hours[$key] ?? ['open' => '08:00', 'close' => '22:00', 'closed' => false];
                    @endphp
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                        <div class="w-32">
                            <span class="font-medium text-gray-900">{{ $dayName }}</span>
                        </div>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="opening_hours[{{ $key }}][closed]" value="1"
                                   {{ ($dayHours['closed'] ?? false) ? 'checked' : '' }}
                                   onchange="toggleDay('{{ $key }}', this.checked)"
                                   class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span class="text-sm text-gray-600">Cerrado</span>
                        </label>

                        <div id="hours-{{ $key }}" class="flex items-center gap-4 {{ ($dayHours['closed'] ?? false) ? 'opacity-50 pointer-events-none' : '' }}">
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-gray-500">Abre:</label>
                                <input type="time" name="opening_hours[{{ $key }}][open]" 
                                       value="{{ $dayHours['open'] ?? '08:00' }}"
                                       class="px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div class="flex items-center gap-2">
                                <label class="text-sm text-gray-500">Cierra:</label>
                                <input type="time" name="opening_hours[{{ $key }}][close]" 
                                       value="{{ $dayHours['close'] ?? '22:00' }}"
                                       class="px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-700">
                        <p class="font-medium">Horarios especiales</p>
                        <p>Los horarios se usan para mostrar información a clientes y para validar reservaciones. No afectan la operación del POS.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('admin.settings.branches') }}" class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Guardar Horarios
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function toggleDay(day, isClosed) {
        const container = document.getElementById('hours-' + day);
        if (isClosed) {
            container.classList.add('opacity-50', 'pointer-events-none');
        } else {
            container.classList.remove('opacity-50', 'pointer-events-none');
        }
    }
</script>
@endpush
@endsection
