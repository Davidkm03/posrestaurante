<x-layouts.app title="Admin">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Copias de Seguridad
            </h2>
            <button onclick="createBackup()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                Crear Backup
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

            <!-- Info -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Backups</p>
                            <p class="text-xl font-bold text-gray-900">{{ $backups->count() ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Último Backup</p>
                            <p class="text-xl font-bold text-gray-900">{{ $lastBackup?->created_at->diffForHumans() ?? 'Nunca' }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Backup Automático</p>
                            <p class="text-xl font-bold text-gray-900">Diario</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Backups -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-gray-50">
                    <h3 class="font-semibold text-gray-900">Historial de Copias de Seguridad</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Archivo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tamaño</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($backups ?? [] as $backup)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <svg class="w-8 h-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                            </svg>
                                            <span class="font-medium text-gray-900 font-mono text-sm">{{ $backup->filename }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $backup->created_at->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $backup->size_formatted ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $backup->type === 'auto' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $backup->type === 'auto' ? 'Automático' : 'Manual' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <button onclick="restoreBackup('{{ $backup->id }}')" class="text-green-600 hover:text-green-800 mr-3" title="Restaurar">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                        </button>
                                        <a href="{{ route('admin.backups.download', ['backup' => $backup->id]) }}" class="text-blue-600 hover:text-blue-800 mr-3" title="Descargar">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                        </a>
                                        <button onclick="deleteBackup('{{ $backup->id }}')" class="text-red-600 hover:text-red-800" title="Eliminar">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">Sin backups</h3>
                                        <p class="mt-1 text-sm text-gray-500">Crea tu primera copia de seguridad.</p>
                                        <div class="mt-6">
                                            <button onclick="createBackup()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                                Crear Backup Ahora
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Configuración -->
            <div class="mt-6 bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Configuración de Backups Automáticos</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Frecuencia</label>
                        <select class="w-full border-gray-300 rounded-lg">
                            <option value="daily" selected>Diario</option>
                            <option value="weekly">Semanal</option>
                            <option value="monthly">Mensual</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Retención (días)</label>
                        <input type="number" value="30" min="7" max="365" class="w-full border-gray-300 rounded-lg">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function createBackup() {
            showAlert('info', '¿Crear Backup?', 'Se creará una nueva copia de seguridad de toda la base de datos.', () => {
                showLoader('Creando copia de seguridad...');
                
                fetch('{{ route("admin.backups.create") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                }).then(response => {
                    hideLoader();
                    if (response.ok) {
                        showAlert('success', '¡Backup Creado!', 'La copia de seguridad se ha creado exitosamente.', () => {
                            window.location.reload();
                        });
                    } else {
                        response.json().then(data => {
                            showAlert('error', 'Error', data.message || 'Error al crear backup');
                        }).catch(() => {
                            showAlert('error', 'Error', 'Error al crear backup');
                        });
                    }
                }).catch(error => {
                    hideLoader();
                    showAlert('error', 'Error de Conexión', 'No se pudo conectar con el servidor.');
                });
            }, true);
        }

        function restoreBackup(id) {
            showDangerConfirm(
                '¿Restaurar Backup?',
                'Estás a punto de restaurar esta copia de seguridad. Todos los datos actuales serán reemplazados por los datos del backup seleccionado.',
                '⚠️ Esta acción NO se puede deshacer. Se creará un backup de seguridad automático antes de restaurar.',
                () => {
                    showLoader('Restaurando backup...');
                    
                    fetch(`{{ url('/admin/backups') }}/${id}/restore`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    }).then(response => response.json().then(data => ({ok: response.ok, data})))
                    .then(({ok, data}) => {
                        hideLoader();
                        if (ok) {
                            showAlert('success', '¡Restauración Completa!', data.message || 'El backup se ha restaurado exitosamente.', () => {
                                window.location.href = '/admin/backups';
                            });
                        } else {
                            showAlert('error', 'Error', data.message || 'Error al restaurar backup');
                        }
                    }).catch(error => {
                        hideLoader();
                        showAlert('error', 'Error de Conexión', 'No se pudo conectar con el servidor.');
                    });
                }
            );
        }

        function deleteBackup(id) {
            showDangerConfirm(
                '¿Eliminar Backup?',
                'Esta copia de seguridad será eliminada permanentemente.',
                null,
                () => {
                    showLoader('Eliminando backup...');
                    
                    fetch(`{{ url('/admin/backups') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    }).then(response => {
                        hideLoader();
                        if (response.ok) {
                            showAlert('success', '¡Eliminado!', 'La copia de seguridad ha sido eliminada.', () => {
                                window.location.reload();
                            });
                        } else {
                            showAlert('error', 'Error', 'No se pudo eliminar el backup.');
                        }
                    }).catch(error => {
                        hideLoader();
                        showAlert('error', 'Error de Conexión', 'No se pudo conectar con el servidor.');
                    });
                }
            );
        }
    </script>
</x-layouts.app>
