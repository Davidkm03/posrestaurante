<x-layouts.app title="Editar Cliente: {{ $customer->name }}">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.customers.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Cliente</h1>
                <p class="text-sm text-gray-500">{{ $customer->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
            @csrf
            @method('PUT')

            <x-card title="Tipo de Cliente" class="mb-6">
                <div class="flex gap-4">
                    @foreach($customerTypes as $type)
                        <label class="flex-1 flex items-center gap-3 p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                            <input type="radio" name="customer_type" value="{{ $type->value }}" class="text-blue-600" required 
                                   {{ old('customer_type', $customer->customer_type) == $type->value ? 'checked' : '' }}>
                            <div>
                                <p class="font-medium">{{ $type->label() }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </x-card>

            <x-card title="Información del Cliente" class="mb-6">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input name="first_name" label="Nombre" required :value="$customer->first_name" />
                        <x-input name="last_name" label="Apellido" :value="$customer->last_name" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-select name="document_type" label="Tipo de documento" required 
                                  :options="collect($documentTypes)->mapWithKeys(fn($d) => [$d->value => $d->label()])->toArray()" 
                                  :selected="$customer->document_type" />
                        <x-input name="document_number" label="Número de documento" required :value="$customer->document_number" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input type="email" name="email" label="Correo electrónico" :value="$customer->email" />
                        <x-input name="phone" label="Teléfono" :value="$customer->phone" />
                    </div>

                    <x-input name="address" label="Dirección" :value="$customer->address" />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input name="city" label="Ciudad" :value="$customer->city" />
                        <x-input name="department" label="Departamento" :value="$customer->department" />
                    </div>
                </div>
            </x-card>

            <x-card title="Información Tributaria (Empresas)" class="mb-6" id="business-fields">
                <div class="space-y-4">
                    <x-input name="business_name" label="Razón social / Nombre comercial" :value="$customer->business_name" />
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-select name="tax_regime" label="Régimen tributario" :options="[
                            'simplified' => 'Régimen Simple',
                            'common' => 'Régimen Común',
                            'special' => 'Régimen Especial',
                        ]" :selected="$customer->tax_regime" placeholder="Seleccionar..." />
                        <x-input name="fiscal_responsibilities" label="Responsabilidades (O-13, etc.)" :value="$customer->fiscal_responsibilities" />
                    </div>
                </div>
            </x-card>

            <x-card>
                <x-textarea name="notes" label="Notas internas" rows="2" :value="$customer->notes" />

                <x-slot name="footer">
                    <div class="flex justify-between">
                        @if($customer->orders()->count() == 0)
                            <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" 
                                  onsubmit="return confirm('¿Eliminar este cliente?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2.5 text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Eliminar
                                </button>
                            </form>
                        @else
                            <div></div>
                        @endif
                        
                        <div class="flex gap-3">
                            <a href="{{ route('admin.customers.index') }}" class="px-4 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Cancelar</a>
                            <x-button type="submit">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Guardar Cambios
                            </x-button>
                        </div>
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
