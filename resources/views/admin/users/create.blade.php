<x-layouts.app title="Crear Usuario">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Crear Usuario</h1>
        </div>
    </x-slot>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <x-card title="Información Personal">
                    <div class="space-y-4">
                        <x-input name="name" label="Nombre completo" required />
                        <div class="grid grid-cols-2 gap-4">
                            <x-input type="email" name="email" label="Correo electrónico" required />
                            <x-input name="phone" label="Teléfono" />
                        </div>
                        <x-input name="position" label="Cargo" placeholder="Ej: Mesero, Cajero" />
                    </div>
                </x-card>

                <x-card title="Seguridad">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <x-input type="password" name="password" label="Contraseña" required />
                            <x-input type="password" name="password_confirmation" label="Confirmar contraseña" required />
                        </div>
                        <x-input name="pin" label="PIN de acceso rápido" required maxlength="4" placeholder="4 dígitos" hint="PIN para acceso rápido al POS" />
                    </div>
                </x-card>

                <x-card title="Sucursales">
                    <p class="text-sm text-gray-500 mb-4">Selecciona las sucursales a las que tendrá acceso</p>
                    <div class="space-y-3">
                        @foreach($branches as $branch)
                            <label class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" name="branches[]" value="{{ $branch->id }}" class="rounded border-gray-300 text-blue-600">
                                    <span class="font-medium">{{ $branch->name }}</span>
                                </div>
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="radio" name="default_branch" value="{{ $branch->id }}" class="text-blue-600">
                                    <span class="text-gray-500">Principal</span>
                                </label>
                            </label>
                        @endforeach
                    </div>
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card title="Rol">
                    <div class="space-y-2">
                        @foreach($roles as $role)
                            <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="role" value="{{ $role->value }}" class="text-blue-600" required>
                                <div>
                                    <p class="font-medium">{{ $role->label() }}</p>
                                    <p class="text-xs text-gray-500">{{ $role->description() }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </x-card>

                <x-card title="Estado">
                    <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="font-medium">Usuario activo</span>
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-blue-600">
                    </label>
                </x-card>

                <div class="flex gap-3">
                    <a href="{{ route('admin.users.index') }}" class="flex-1 px-4 py-2 text-center text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</a>
                    <x-button type="submit" class="flex-1 justify-center">Guardar</x-button>
                </div>
            </div>
        </div>
    </form>
</x-layouts.app>
