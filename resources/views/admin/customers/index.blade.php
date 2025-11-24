<x-layouts.app title="Clientes">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Clientes</h1>
                <p class="text-sm text-gray-500">Base de datos de clientes</p>
            </div>
            <a href="{{ route('admin.customers.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nuevo Cliente
            </a>
        </div>
    </x-slot>

    <x-card class="mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <x-input name="search" placeholder="Buscar por nombre, documento, teléfono..." :value="request('search')" class="w-72" />
            <x-select name="type" placeholder="Todos los tipos" :options="collect($customerTypes)->mapWithKeys(fn($t) => [$t->value => $t->label()])->toArray()" :selected="request('type')" class="w-48" />
            <x-button type="submit" variant="secondary">Buscar</x-button>
        </form>
    </x-card>

    <x-card :padding="false">
        <x-table>
            <x-slot name="head">
                <x-th>Cliente</x-th>
                <x-th>Documento</x-th>
                <x-th>Contacto</x-th>
                <x-th align="center">Órdenes</x-th>
                <x-th align="right">Total Compras</x-th>
                <x-th align="right">Acciones</x-th>
            </x-slot>

            @forelse($customers as $customer)
                <tr>
                    <x-td>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="font-medium text-blue-600">{{ substr($customer->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $customer->name }}</p>
                                <x-badge type="{{ $customer->customer_type === 'business' ? 'purple' : 'secondary' }}" size="xs">
                                    {{ $customer->customer_type === 'business' ? 'Empresa' : 'Persona' }}
                                </x-badge>
                            </div>
                        </div>
                    </x-td>
                    <x-td>
                        <p class="text-sm">{{ $customer->document_type }}</p>
                        <p class="font-mono">{{ $customer->document_number }}</p>
                    </x-td>
                    <x-td>
                        <p class="text-sm">{{ $customer->phone ?? '-' }}</p>
                        <p class="text-sm text-gray-500">{{ $customer->email ?? '-' }}</p>
                    </x-td>
                    <x-td align="center">
                        <span class="font-medium">{{ $customer->orders_count }}</span>
                    </x-td>
                    <x-td align="right">
                        <span class="font-semibold">${{ number_format($customer->orders_sum_total ?? 0, 0, ',', '.') }}</span>
                    </x-td>
                    <x-td align="right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('admin.customers.edit', $customer) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                        </div>
                    </x-td>
                </tr>
            @empty
                <tr><td colspan="6"><x-empty-state title="No hay clientes" description="Los clientes se crearán al facturar" /></td></tr>
            @endforelse
        </x-table>

        @if($customers->hasPages())
            <div class="px-6 py-4 border-t">{{ $customers->links() }}</div>
        @endif
    </x-card>
</x-layouts.app>
