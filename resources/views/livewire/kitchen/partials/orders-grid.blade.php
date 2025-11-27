{{-- Grid de Órdenes Activas (Pendientes + En Preparación) --}}
@php
    $activeOrders = $pendingOrders->merge($preparingOrders);
@endphp

@if($activeOrders->count() === 0)
    {{-- Estado Vacío --}}
    <div class="flex-1 flex flex-col items-center justify-center text-gray-500">
        <svg class="w-24 h-24 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <p class="text-2xl font-semibold">Sin órdenes activas</p>
        <p class="text-gray-600 mt-2">Las nuevas órdenes aparecerán aquí</p>
    </div>
@else
    {{-- Grid de Órdenes --}}
    <div class="flex-1 overflow-y-auto">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3">
            @foreach($activeOrders->sortBy('created_at') as $order)
                @include('livewire.kitchen.partials.order-card-compact', ['order' => $order])
            @endforeach
        </div>
    </div>
@endif
