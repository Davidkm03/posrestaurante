<x-layouts.app title="Crear Cliente">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.customers.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Crear Cliente</h1>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <form action="{{ route('admin.customers.store') }}" method="POST">
            @csrf

            <x-card title="Tipo de Cliente" class="mb-6">
                <div class="flex gap-4">
                    @foreach($customerTypes as $type)
                        <label class="flex-1 flex items-center gap-3 p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                            <input type="radio" name="customer_type" value="{{ $type->value }}" class="text-blue-600" required {{ $loop->first ? 'checked' : '' }}>
                            <div>
                                <p class="font-medium">{{ $type->label() }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </x-card>

            <x-card title="Información del Cliente" class="mb-6">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <x-input name="first_name" label="Nombre" required placeholder="Juan" />
                        <x-input name="last_name" label="Apellido" placeholder="Pérez García" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <x-select name="document_type" label="Tipo de documento" required :options="collect($documentTypes)->mapWithKeys(fn($d) => [$d->value => $d->label()])->toArray()" />
                        <x-input name="document_number" label="Número de documento" required />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <x-input type="email" name="email" label="Correo electrónico" />
                        <x-input name="phone" label="Teléfono" />
                    </div>

                    <x-input name="address" label="Dirección" />

                    <div class="grid grid-cols-2 gap-4">
                        <x-input name="city" label="Ciudad" />
                        <x-input name="department" label="Departamento" />
                    </div>
                </div>
            </x-card>

            <x-card title="Información Tributaria (Empresas)" class="mb-6" id="business-fields">
                <div class="space-y-4">
                    <x-input name="business_name" label="Razón social / Nombre comercial" />
                    <div class="grid grid-cols-2 gap-4">
                        <x-select name="tax_regime" label="Régimen tributario" :options="[
                            'simplified' => 'Régimen Simple',
                            'common' => 'Régimen Común',
                            'special' => 'Régimen Especial',
                        ]" placeholder="Seleccionar..." />
                        <x-input name="fiscal_responsibilities" label="Responsabilidades (O-13, etc.)" />
                    </div>
                </div>
            </x-card>

            <x-card>
                <x-textarea name="notes" label="Notas internas" rows="2" />

                <x-slot name="footer">
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</a>
                        <x-button type="submit">Guardar Cliente</x-button>
                    </div>
                </x-slot>
            </x-card>
        </form>
    </div>

    @push('scripts')
    <script>
        document.querySelectorAll('input[name="customer_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('business-fields').style.display =
                    this.value === 'business' ? 'block' : 'none';
            });
        });
        // Initial state
        document.getElementById('business-fields').style.display =
            document.querySelector('input[name="customer_type"]:checked')?.value === 'business' ? 'block' : 'none';
    </script>
    @endpush
</x-layouts.app>
