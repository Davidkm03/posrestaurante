<x-layouts.app title="Editar Usuario">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Usuario</h1>
                <p class="text-sm text-gray-500">Actualiza la información del usuario</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <x-card>
            <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Información Personal -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Información Personal</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nombre Completo <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="name" 
                                    id="name" 
                                    value="{{ old('name', $user->name) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                    required
                                >
                                @error('name')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    name="email" 
                                    id="email" 
                                    value="{{ old('email', $user->email) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                                    required
                                >
                                @error('email')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                        Teléfono
                                    </label>
                                    <input 
                                        type="text" 
                                        name="phone" 
                                        id="phone" 
                                        value="{{ old('phone', $user->phone) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                                    >
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="position" class="block text-sm font-medium text-gray-700 mb-1">
                                        Cargo
                                    </label>
                                    <input 
                                        type="text" 
                                        name="position" 
                                        id="position" 
                                        value="{{ old('position', $user->position) }}"
                                        placeholder="Mesero, Cajero, etc."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('position') border-red-500 @enderror"
                                    >
                                    @error('position')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="avatar" class="block text-sm font-medium text-gray-700 mb-1">
                                    Avatar
                                </label>
                                @if($user->avatar)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($user->avatar) }}" class="w-20 h-20 rounded-full object-cover">
                                    </div>
                                @endif
                                <input 
                                    type="file" 
                                    name="avatar" 
                                    id="avatar" 
                                    accept="image/*"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('avatar') border-red-500 @enderror"
                                >
                                @error('avatar')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Seguridad -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Seguridad</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nueva Contraseña
                                </label>
                                <input 
                                    type="password" 
                                    name="password" 
                                    id="password" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                                >
                                @error('password')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Dejar en blanco para mantener la actual</p>
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                    Confirmar Contraseña
                                </label>
                                <input 
                                    type="password" 
                                    name="password_confirmation" 
                                    id="password_confirmation" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                            </div>

                            <div>
                                <label for="pin" class="block text-sm font-medium text-gray-700 mb-1">
                                    PIN (4 dígitos) <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="pin" 
                                    id="pin" 
                                    value="{{ old('pin') }}"
                                    maxlength="4"
                                    pattern="[0-9]{4}"
                                    placeholder="Dejar en blanco para mantener el actual"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pin') border-red-500 @enderror"
                                >
                                @error('pin')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Dejar en blanco para mantener el actual</p>
                            </div>
                        </div>
                    </div>

                    <!-- Roles y Permisos -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Roles y Permisos</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Rol <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-2">
                                @foreach($roles as $role)
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input 
                                            type="radio" 
                                            name="role" 
                                            value="{{ $role->value }}"
                                            {{ old('role', $user->roles->first()?->name) === $role->value ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 focus:ring-blue-500"
                                            required
                                        >
                                        <span class="ml-3">
                                            <span class="block text-sm font-medium text-gray-900">{{ $role->label() }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @error('role')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Sucursales -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Sucursales</h3>
                        
                        <div class="space-y-2">
                            @foreach($branches as $branch)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="branches[]" 
                                        value="{{ $branch->id }}"
                                        {{ in_array($branch->id, old('branches', $user->branches->pluck('id')->toArray())) ? 'checked' : '' }}
                                        class="w-4 h-4 text-blue-600 focus:ring-blue-500 rounded"
                                    >
                                    <span class="ml-3 text-sm text-gray-900">{{ $branch->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('branches')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Comisión -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración Adicional</h3>
                        
                        <div>
                            <label for="commission_percentage" class="block text-sm font-medium text-gray-700 mb-1">
                                Porcentaje de Comisión (%)
                            </label>
                            <input 
                                type="number" 
                                name="commission_percentage" 
                                id="commission_percentage" 
                                value="{{ old('commission_percentage', $user->commission_percentage) }}"
                                min="0"
                                max="100"
                                step="0.01"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('commission_percentage') border-red-500 @enderror"
                            >
                            @error('commission_percentage')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="is_active" class="text-sm font-medium text-gray-700">Usuario Activo</label>
                                <p class="text-xs text-gray-500">El usuario podrá acceder al sistema</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input 
                                    type="checkbox" 
                                    name="is_active" 
                                    id="is_active" 
                                    value="1"
                                    {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                    class="sr-only peer"
                                >
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-between gap-4 pt-6 mt-6 border-t border-gray-200">
                    @if($user->id !== auth()->id())
                        <button 
                            type="button"
                            onclick="if(confirm('¿Estás seguro de eliminar este usuario?')) { document.getElementById('delete-form').submit(); }"
                            class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg"
                        >
                            Eliminar Usuario
                        </button>
                    @else
                        <div></div>
                    @endif
                    <div class="flex gap-3">
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            Cancelar
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>

            <!-- Formulario de eliminación -->
            @if($user->id !== auth()->id())
                <form id="delete-form" action="{{ route('admin.users.destroy', $user) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        </x-card>
    </div>
</x-layouts.app>
