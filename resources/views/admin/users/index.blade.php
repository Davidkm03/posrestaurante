<x-layouts.app title="Usuarios">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Usuarios</h1>
                <p class="text-sm text-gray-500">Gestión de usuarios y permisos</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nuevo Usuario
            </a>
        </div>
    </x-slot>

    <x-card class="mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <x-input name="search" placeholder="Buscar por nombre o email..." :value="request('search')" class="w-64" />
            <x-select name="role" placeholder="Todos los roles" :options="collect($roles)->mapWithKeys(fn($r) => [$r->value => $r->label()])->toArray()" :selected="request('role')" class="w-48" />
            <x-select name="status" placeholder="Todos los estados" :options="['active' => 'Activos', 'inactive' => 'Inactivos']" :selected="request('status')" class="w-40" />
            <x-button type="submit" variant="secondary">Filtrar</x-button>
        </form>
    </x-card>

    <x-card :padding="false">
        <x-table>
            <x-slot name="head">
                <x-th>Usuario</x-th>
                <x-th>Rol</x-th>
                <x-th>Sucursales</x-th>
                <x-th align="center">Estado</x-th>
                <x-th align="right">Acciones</x-th>
            </x-slot>

            @forelse($users as $user)
                <tr>
                    <x-td>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                <span class="font-medium text-gray-600">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                    </x-td>
                    <x-td>
                        @foreach($user->roles as $role)
                            <x-badge :type="match($role->name) {
                                'super_admin' => 'danger',
                                'admin' => 'primary',
                                'manager' => 'purple',
                                'cashier' => 'success',
                                'waiter' => 'warning',
                                'kitchen' => 'info',
                                default => 'secondary'
                            }">{{ $role->name }}</x-badge>
                        @endforeach
                    </x-td>
                    <x-td>
                        <div class="flex flex-wrap gap-1">
                            @foreach($user->branches->take(2) as $branch)
                                <span class="text-xs px-2 py-1 bg-gray-100 rounded">{{ $branch->name }}</span>
                            @endforeach
                            @if($user->branches->count() > 2)
                                <span class="text-xs text-gray-500">+{{ $user->branches->count() - 2 }}</span>
                            @endif
                        </div>
                    </x-td>
                    <x-td align="center">
                        <button onclick="document.getElementById('toggle-{{ $user->id }}').submit()">
                            @if($user->is_active)
                                <x-badge type="success" dot>Activo</x-badge>
                            @else
                                <x-badge type="danger" dot>Inactivo</x-badge>
                            @endif
                        </button>
                        <form id="toggle-{{ $user->id }}" action="{{ route('admin.users.toggle-active', $user) }}" method="POST" class="hidden">@csrf @method('PATCH')</form>
                    </x-td>
                    <x-td align="right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('¿Eliminar este usuario?')">
                                    @csrf @method('DELETE')
                                    <button class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </x-td>
                </tr>
            @empty
                <tr><td colspan="5"><x-empty-state title="No hay usuarios" /></td></tr>
            @endforelse
        </x-table>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t">{{ $users->links() }}</div>
        @endif
    </x-card>
</x-layouts.app>
