<x-layouts.app title="Admin">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Resoluciones de Facturación DIAN
            </h2>
            <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nueva Resolución
            </button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Alerta de estado -->
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                <div class="flex">
                    <svg class="w-5 h-5 text-yellow-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <h3 class="font-medium text-yellow-800">Importante</h3>
                        <p class="text-sm text-yellow-700 mt-1">
                            Las resoluciones de facturación electrónica deben estar autorizadas por la DIAN. 
                            Asegúrate de registrar los datos exactos de tu resolución vigente.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Lista de Resoluciones -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Resolución</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prefijo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rango</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vigencia</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Consecutivo Actual</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($resolutions ?? [] as $resolution)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ $resolution->resolution_number }}</div>
                                        <div class="text-sm text-gray-500">{{ $resolution->resolution_date?->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-gray-900">
                                        {{ $resolution->prefix ?? 'Sin prefijo' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $resolution->range_from }} - {{ $resolution->range_to }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="text-gray-900">{{ $resolution->valid_from?->format('d/m/Y') }}</div>
                                        <div class="text-gray-500">al {{ $resolution->valid_to?->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-lg font-bold text-blue-600">{{ $resolution->current_number ?? $resolution->range_from }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ number_format((($resolution->current_number - $resolution->range_from) / ($resolution->range_to - $resolution->range_from)) * 100, 1) }}% usado
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($resolution->is_active && $resolution->valid_to >= now())
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Activa</span>
                                        @elseif($resolution->valid_to < now())
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Vencida</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactiva</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <a href="{{ route('admin.settings.resolutions.edit', $resolution) }}" class="text-blue-600 hover:text-blue-800 mr-3">Editar</a>
                                        <button onclick="deleteResolution({{ $resolution->id }})" class="text-red-600 hover:text-red-800">Eliminar</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay resoluciones</h3>
                                        <p class="mt-1 text-sm text-gray-500">Registra tu resolución de facturación electrónica DIAN.</p>
                                        <div class="mt-6">
                                            <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                                Agregar Resolución
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="resolutionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold">Nueva Resolución DIAN</h3>
            </div>
            <form method="POST" action="{{ route('admin.settings.resolutions.store') }}">
                @csrf
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Número de Resolución</label>
                            <input type="text" name="resolution_number" required
                                   class="w-full border-gray-300 rounded-lg"
                                   placeholder="18764000001">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Resolución</label>
                            <input type="date" name="resolution_date" required
                                   class="w-full border-gray-300 rounded-lg">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prefijo (Opcional)</label>
                        <input type="text" name="prefix"
                               class="w-full border-gray-300 rounded-lg"
                               placeholder="FE, SETP, etc.">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                            <input type="number" name="range_from" required min="1"
                                   class="w-full border-gray-300 rounded-lg"
                                   placeholder="1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                            <input type="number" name="range_to" required min="1"
                                   class="w-full border-gray-300 rounded-lg"
                                   placeholder="10000">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Válida desde</label>
                            <input type="date" name="valid_from" required
                                   class="w-full border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Válida hasta</label>
                            <input type="date" name="valid_to" required
                                   class="w-full border-gray-300 rounded-lg">
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" checked class="rounded text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Activar como resolución principal</span>
                        </label>
                    </div>
                </div>
                <div class="p-6 border-t bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Guardar Resolución
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openModal() {
            document.getElementById('resolutionModal').classList.remove('hidden');
            document.getElementById('resolutionModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('resolutionModal').classList.add('hidden');
            document.getElementById('resolutionModal').classList.remove('flex');
        }

        function deleteResolution(id) {
            if (confirm('¿Eliminar esta resolución? Esta acción no se puede deshacer.')) {
                fetch(`/admin/settings/resolutions/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(() => window.location.reload());
            }
        }
    </script>
    @endpush
</x-layouts.app>
