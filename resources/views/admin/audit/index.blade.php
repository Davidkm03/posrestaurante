<x-layouts.app title="Admin">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registro de Auditoría
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filtros -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <form method="GET" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Buscar por usuario o acción..."
                               class="w-full border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <select name="action" class="border-gray-300 rounded-lg">
                            <option value="">Todas las acciones</option>
                            <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>Crear</option>
                            <option value="update" {{ request('action') === 'update' ? 'selected' : '' }}>Actualizar</option>
                            <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>Eliminar</option>
                            <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Inicio sesión</option>
                            <option value="logout" {{ request('action') === 'logout' ? 'selected' : '' }}>Cierre sesión</option>
                        </select>
                    </div>
                    <div>
                        <input type="date" name="from" value="{{ request('from') }}" 
                               class="border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <input type="date" name="to" value="{{ request('to') }}" 
                               class="border-gray-300 rounded-lg">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Filtrar
                    </button>
                    <a href="{{ route('admin.audit.export') }}?{{ http_build_query(request()->all()) }}" 
                       class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Exportar
                    </a>
                </form>
            </div>

            <!-- Lista de eventos -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha/Hora</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recurso</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detalles</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($logs ?? [] as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-sm font-medium text-gray-600">
                                                {{ substr($log->user->name ?? 'S', 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $log->user->name ?? 'Sistema' }}</div>
                                                <div class="text-xs text-gray-500">{{ $log->user->email ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full
                                            {{ $log->action === 'create' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $log->action === 'update' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $log->action === 'delete' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $log->action === 'login' ? 'bg-purple-100 text-purple-800' : '' }}
                                            {{ $log->action === 'logout' ? 'bg-gray-100 text-gray-800' : '' }}">
                                            {{ ucfirst($log->action) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $log->auditable_type ? class_basename($log->auditable_type) : '-' }}
                                        @if($log->auditable_id)
                                            <span class="text-gray-500">#{{ $log->auditable_id }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                        {{ $log->description ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                        {{ $log->ip_address ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">Sin registros</h3>
                                        <p class="mt-1 text-sm text-gray-500">No hay eventos de auditoría para mostrar.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($logs) && method_exists($logs, 'hasPages') && $logs->hasPages())
                    <div class="px-6 py-4 border-t">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
