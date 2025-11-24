@if($isOpen)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70" wire:click.self="close">
    <div class="bg-gray-800 rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <!-- Header -->
        <div class="p-4 border-b border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Nuevo Cliente</h3>
                <button wire:click="close" class="p-2 text-gray-400 hover:text-white rounded-lg hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Body -->
        <form wire:submit="save" class="p-4 space-y-4">
            <!-- Document -->
            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Tipo Doc.</label>
                    <select wire:model="documentType" class="w-full bg-gray-700 text-white rounded-lg border border-gray-600 p-2 text-sm">
                        @foreach($documentTypes as $key => $label)
                            <option value="{{ $key }}">{{ $key }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm text-gray-400 mb-1">Número Documento</label>
                    <input type="text"
                           wire:model="documentNumber"
                           wire:blur="searchByDocument"
                           placeholder="Número de documento"
                           class="w-full bg-gray-700 text-white rounded-lg border border-gray-600 p-2 text-sm @error('documentNumber') border-red-500 @enderror">
                    @error('documentNumber')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Name -->
            <div>
                <label class="block text-sm text-gray-400 mb-1">Nombre Completo / Razón Social</label>
                <input type="text"
                       wire:model="name"
                       placeholder="Nombre del cliente"
                       class="w-full bg-gray-700 text-white rounded-lg border border-gray-600 p-2 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contact -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Teléfono</label>
                    <input type="tel"
                           wire:model="phone"
                           placeholder="Teléfono"
                           class="w-full bg-gray-700 text-white rounded-lg border border-gray-600 p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Email</label>
                    <input type="email"
                           wire:model="email"
                           placeholder="correo@ejemplo.com"
                           class="w-full bg-gray-700 text-white rounded-lg border border-gray-600 p-2 text-sm @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Address -->
            <div>
                <label class="block text-sm text-gray-400 mb-1">Dirección</label>
                <input type="text"
                       wire:model="address"
                       placeholder="Dirección (opcional)"
                       class="w-full bg-gray-700 text-white rounded-lg border border-gray-600 p-2 text-sm">
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-4">
                <button type="button"
                        wire:click="close"
                        class="flex-1 py-2 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg">
                    Guardar Cliente
                </button>
            </div>
        </form>
    </div>
</div>
@endif
