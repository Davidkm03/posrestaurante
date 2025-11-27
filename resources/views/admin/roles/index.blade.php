<x-layouts.app title="Admin">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Roles y Permisos
            </h2>
            <button onclick="openRoleModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nuevo Rol
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Lista de Roles -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="p-4 border-b bg-gray-50">
                            <h3 class="font-semibold text-gray-900">Roles del Sistema</h3>
                        </div>
                        <div class="divide-y">
                            @forelse($roles ?? [] as $role)
                                <div class="p-4 hover:bg-gray-50 cursor-pointer {{ ($selectedRole ?? null)?->id === $role->id ? 'bg-blue-50 border-l-4 border-blue-500' : '' }}"
                                     onclick="selectRole({{ $role->id }})">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $role->name }}</h4>
                                            <p class="text-sm text-gray-500">{{ $role->users_count ?? 0 }} usuarios</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @if(!in_array($role->name, ['administrador', 'gerente']))
                                                <button onclick="event.stopPropagation(); editRole({{ $role->id }})" class="p-1 text-gray-400 hover:text-blue-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                <button onclick="event.stopPropagation(); deleteRole({{ $role->id }})" class="p-1 text-gray-400 hover:text-red-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="text-xs text-gray-400">Sistema</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-gray-500">
                                    <p>No hay roles definidos</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Permisos del Rol Seleccionado -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="p-4 border-b bg-gray-50 flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900">
                                Permisos: <span id="selectedRoleName">{{ ($selectedRole ?? null)?->name ?? 'Selecciona un rol' }}</span>
                            </h3>
                            @if($selectedRole ?? null)
                                <button onclick="savePermissions()" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                                    Guardar Cambios
                                </button>
                            @endif
                        </div>
                        
                        <div class="p-6">
                            @if($selectedRole ?? null)
                                <form id="permissionsForm">
                                    @foreach($permissionGroups ?? [] as $group => $permissions)
                                        <div class="mb-6">
                                            <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                                                <input type="checkbox" class="mr-2 rounded" onchange="toggleGroup(this, '{{ $group }}')">
                                                {{ ucfirst($group) }}
                                            </h4>
                                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 ml-6">
                                                @foreach($permissions as $permission)
                                                    <label class="flex items-center">
                                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                               class="rounded text-blue-600 permission-{{ $group }}"
                                                               {{ $selectedRole->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                                        <span class="ml-2 text-sm text-gray-700">{{ $permission->name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </form>
                            @else
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Selecciona un rol</h3>
                                    <p class="mt-1 text-sm text-gray-500">Haz clic en un rol para ver y editar sus permisos.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nuevo Rol -->
    <div id="roleModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold" id="roleModalTitle">Nuevo Rol</h3>
            </div>
            <form id="roleForm" method="POST" action="{{ route('admin.roles.store') }}">
                @csrf
                <div id="roleMethodField"></div>
                <div class="p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Rol</label>
                    <input type="text" name="name" id="roleName" required
                           class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Ej: Supervisor">
                    <p class="mt-1 text-sm text-gray-500">El nombre debe ser único y descriptivo.</p>
                </div>
                <div class="p-6 border-t bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="closeRoleModal()" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function selectRole(roleId) {
            window.location.href = `{{ route('admin.roles.index') }}?role=${roleId}`;
        }

        function openRoleModal() {
            document.getElementById('roleModal').classList.remove('hidden');
            document.getElementById('roleModal').classList.add('flex');
            document.getElementById('roleModalTitle').textContent = 'Nuevo Rol';
            document.getElementById('roleForm').reset();
            document.getElementById('roleMethodField').innerHTML = '';
            document.getElementById('roleForm').action = '{{ route("admin.roles.store") }}';
        }

        function closeRoleModal() {
            document.getElementById('roleModal').classList.add('hidden');
            document.getElementById('roleModal').classList.remove('flex');
        }

        function editRole(id) {
            document.getElementById('roleModal').classList.remove('hidden');
            document.getElementById('roleModal').classList.add('flex');
            document.getElementById('roleModalTitle').textContent = 'Editar Rol';
            document.getElementById('roleMethodField').innerHTML = '@method("PUT")';
            document.getElementById('roleForm').action = `/admin/roles/${id}`;
        }

        function deleteRole(id) {
            if (confirm('¿Eliminar este rol? Los usuarios con este rol perderán sus permisos.')) {
                fetch(`/admin/roles/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(() => window.location.reload());
            }
        }

        function toggleGroup(checkbox, group) {
            document.querySelectorAll(`.permission-${group}`).forEach(cb => {
                cb.checked = checkbox.checked;
            });
        }

        function savePermissions() {
            const form = document.getElementById('permissionsForm');
            const formData = new FormData(form);
            
            fetch(`/admin/roles/{{ ($selectedRole ?? null)?->id }}/permissions`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    permissions: formData.getAll('permissions[]')
                })
            }).then(response => {
                if (response.ok) {
                    alert('Permisos actualizados correctamente');
                }
            });
        }
    </script>
    @endpush
</x-layouts.app>
