<x-layouts.app title="Admin">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Modificadores de Productos
            </h2>
            <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nuevo Modificador
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

            <!-- Grupos de Modificadores -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">Grupos de Modificadores</h3>
                    <p class="text-sm text-gray-500 mt-1">Agrupa modificadores para facilitar su uso (ej: "Término de carne", "Extras")</p>
                </div>
                
                <div class="divide-y">
                    @forelse($modifierGroups ?? [] as $group)
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $group->name }}</h4>
                                    <p class="text-sm text-gray-500">
                                        {{ $group->modifiers_count ?? 0 }} opciones
                                        @if($group->is_required)
                                            <span class="ml-2 px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">Requerido</span>
                                        @endif
                                        @if($group->allow_multiple)
                                            <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">Múltiple</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button onclick="editGroup({{ $group->id }})" class="p-2 text-gray-400 hover:text-blue-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="deleteGroup({{ $group->id }})" class="p-2 text-gray-400 hover:text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Modificadores del grupo -->
                            @if($group->modifiers && $group->modifiers->count() > 0)
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach($group->modifiers as $modifier)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700">
                                            {{ $modifier->name }}
                                            @if($modifier->price > 0)
                                                <span class="ml-1 text-green-600">+${{ number_format($modifier->price, 0) }}</span>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay modificadores</h3>
                            <p class="mt-1 text-sm text-gray-500">Comienza creando un grupo de modificadores.</p>
                            <div class="mt-6">
                                <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                    Crear Grupo de Modificadores
                                </button>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Lista de todos los modificadores -->
            <div class="mt-6 bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">Todos los Modificadores</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grupo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio Extra</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($modifiers ?? [] as $modifier)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                        {{ $modifier->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                        {{ $modifier->group->name ?? 'Sin grupo' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($modifier->price > 0)
                                            <span class="text-green-600">+${{ number_format($modifier->price, 0) }}</span>
                                        @else
                                            <span class="text-gray-400">Sin costo</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $modifier->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $modifier->is_active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <button onclick="editModifier({{ $modifier->id }})" class="text-blue-600 hover:text-blue-800 mr-3">Editar</button>
                                        <button onclick="deleteModifier({{ $modifier->id }})" class="text-red-600 hover:text-red-800">Eliminar</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        No hay modificadores creados
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para crear/editar -->
    <div id="modifierModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold" id="modalTitle">Nuevo Grupo de Modificadores</h3>
            </div>
            <form id="modifierForm" method="POST" action="{{ route('admin.modifiers.store') }}">
                @csrf
                <div id="methodField"></div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Grupo</label>
                        <input type="text" name="name" id="groupName" required
                               class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Ej: Término de carne">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_required" id="isRequired" class="rounded text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Selección requerida</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="allow_multiple" id="allowMultiple" class="rounded text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Permitir múltiples</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Opciones</label>
                        <div id="optionsContainer" class="space-y-2">
                            <div class="flex gap-2">
                                <input type="text" name="options[0][name]" placeholder="Nombre opción" 
                                       class="flex-1 border-gray-300 rounded-lg text-sm">
                                <input type="number" name="options[0][price]" placeholder="Precio extra" 
                                       class="w-28 border-gray-300 rounded-lg text-sm" step="100" min="0">
                                <button type="button" onclick="removeOption(this)" class="p-2 text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <button type="button" onclick="addOption()" class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                            + Agregar opción
                        </button>
                    </div>
                </div>
                <div class="p-6 border-t bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
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
        let optionIndex = 1;

        function openModal() {
            document.getElementById('modifierModal').classList.remove('hidden');
            document.getElementById('modifierModal').classList.add('flex');
            document.getElementById('modalTitle').textContent = 'Nuevo Grupo de Modificadores';
            document.getElementById('modifierForm').reset();
            document.getElementById('methodField').innerHTML = '';
        }

        function closeModal() {
            document.getElementById('modifierModal').classList.add('hidden');
            document.getElementById('modifierModal').classList.remove('flex');
        }

        function addOption() {
            const container = document.getElementById('optionsContainer');
            const div = document.createElement('div');
            div.className = 'flex gap-2';
            div.innerHTML = `
                <input type="text" name="options[${optionIndex}][name]" placeholder="Nombre opción" 
                       class="flex-1 border-gray-300 rounded-lg text-sm">
                <input type="number" name="options[${optionIndex}][price]" placeholder="Precio extra" 
                       class="w-28 border-gray-300 rounded-lg text-sm" step="100" min="0">
                <button type="button" onclick="removeOption(this)" class="p-2 text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            container.appendChild(div);
            optionIndex++;
        }

        function removeOption(btn) {
            btn.parentElement.remove();
        }

        function editGroup(id) {
            // Implementar edición
            window.location.href = `/admin/modifiers/${id}/edit`;
        }

        function deleteGroup(id) {
            if (confirm('¿Eliminar este grupo y todos sus modificadores?')) {
                fetch(`/admin/modifiers/${id}`, {
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
