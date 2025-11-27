<x-layouts.app title="Calendario de Reservaciones">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Calendario de Reservaciones</h1>
                <p class="text-sm text-gray-500">Vista de calendario del restaurante</p>
            </div>
            <a href="{{ route('admin.reservations.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver a lista
            </a>
        </div>
    </x-slot>

    <livewire:reservations.reservation-calendar />
</x-layouts.app>
