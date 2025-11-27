<x-layouts.app title="Admin">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.debit-notes.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Nueva Nota de Débito
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.debit-notes.store') }}" id="debitNoteForm">
                @csrf

                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <!-- Header con información -->
                    <div class="p-6 border-b bg-gradient-to-r from-green-50 to-emerald-50">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Nota de Débito</h3>
                                <p class="text-sm text-gray-600">Generar cargo adicional sobre una factura existente</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Selección de factura -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Factura de Referencia <span class="text-red-500">*</span>
                            </label>
                            <select name="invoice_id" id="invoice_id" required
                                    class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                                    onchange="loadInvoiceDetails(this.value)">
                                <option value="">Seleccionar factura...</option>
                                @foreach($invoices ?? [] as $invoice)
                                    <option value="{{ $invoice->id }}" 
                                            data-customer="{{ $invoice->customer->first_name ?? 'Consumidor' }} {{ $invoice->customer->last_name ?? 'Final' }}"
                                            data-total="{{ $invoice->total }}"
                                            {{ old('invoice_id') == $invoice->id ? 'selected' : '' }}>
                                        {{ $invoice->invoice_number }} - ${{ number_format($invoice->total, 0) }} 
                                        ({{ $invoice->customer->first_name ?? 'Consumidor' }} {{ $invoice->customer->last_name ?? 'Final' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('invoice_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Información de la factura seleccionada -->
                        <div id="invoiceInfo" class="hidden p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Información de la Factura</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Cliente:</span>
                                    <span id="customerName" class="font-medium text-gray-900">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Total Factura:</span>
                                    <span id="invoiceTotal" class="font-medium text-gray-900">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Motivo de la nota de débito -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Motivo del Cargo Adicional <span class="text-red-500">*</span>
                            </label>
                            <select name="reason_type" id="reason_type" required
                                    class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 mb-2"
                                    onchange="toggleCustomReason(this.value)">
                                <option value="">Seleccionar motivo...</option>
                                <option value="interest" {{ old('reason_type') == 'interest' ? 'selected' : '' }}>Intereses por mora</option>
                                <option value="adjustment" {{ old('reason_type') == 'adjustment' ? 'selected' : '' }}>Ajuste de precio</option>
                                <option value="additional_service" {{ old('reason_type') == 'additional_service' ? 'selected' : '' }}>Servicio adicional</option>
                                <option value="shipping" {{ old('reason_type') == 'shipping' ? 'selected' : '' }}>Cargo de envío</option>
                                <option value="other" {{ old('reason_type') == 'other' ? 'selected' : '' }}>Otro (especificar)</option>
                            </select>
                            @error('reason_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Descripción detallada -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción Detallada <span class="text-red-500">*</span>
                            </label>
                            <textarea name="reason" id="reason" rows="3" required
                                      class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                                      placeholder="Describe detalladamente el motivo del cargo adicional...">{{ old('reason') }}</textarea>
                            @error('reason')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Items de la nota de débito -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-sm font-medium text-gray-700">
                                    Conceptos a Cargar <span class="text-red-500">*</span>
                                </label>
                                <button type="button" onclick="addItem()" 
                                        class="text-sm text-green-600 hover:text-green-800 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Agregar concepto
                                </button>
                            </div>
                            
                            <div id="itemsContainer" class="space-y-3">
                                <!-- Item template -->
                                <div class="item-row flex gap-3 items-start p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <input type="text" name="items[0][description]" required
                                               class="w-full border-gray-300 rounded-lg text-sm"
                                               placeholder="Descripción del concepto">
                                    </div>
                                    <div class="w-24">
                                        <input type="number" name="items[0][quantity]" value="1" min="1" required
                                               class="w-full border-gray-300 rounded-lg text-sm text-center"
                                               placeholder="Cant." onchange="calculateTotals()">
                                    </div>
                                    <div class="w-32">
                                        <input type="number" name="items[0][unit_price]" min="0" step="1" required
                                               class="w-full border-gray-300 rounded-lg text-sm text-right"
                                               placeholder="Valor" onchange="calculateTotals()">
                                    </div>
                                    <div class="w-32">
                                        <input type="text" readonly
                                               class="w-full bg-gray-100 border-gray-300 rounded-lg text-sm text-right item-subtotal"
                                               value="$0">
                                    </div>
                                    <button type="button" onclick="removeItem(this)" 
                                            class="text-red-500 hover:text-red-700 p-2 opacity-50 cursor-not-allowed" disabled>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Impuestos -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Aplicar IVA
                                </label>
                                <select name="tax_rate" id="tax_rate" 
                                        class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                                        onchange="calculateTotals()">
                                    <option value="0" {{ old('tax_rate', 0) == 0 ? 'selected' : '' }}>Sin IVA (0%)</option>
                                    <option value="5" {{ old('tax_rate') == 5 ? 'selected' : '' }}>IVA 5%</option>
                                    <option value="19" {{ old('tax_rate') == 19 ? 'selected' : '' }}>IVA 19%</option>
                                </select>
                            </div>
                        </div>

                        <!-- Resumen de totales -->
                        <div class="border-t pt-4">
                            <div class="flex justify-end">
                                <div class="w-64 space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Subtotal:</span>
                                        <span id="subtotalDisplay" class="font-medium">$0</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">IVA (<span id="taxRateDisplay">0</span>%):</span>
                                        <span id="taxDisplay" class="font-medium">$0</span>
                                    </div>
                                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                                        <span class="text-gray-900">Total Cargo:</span>
                                        <span id="totalDisplay" class="text-green-600">$0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campos ocultos para totales -->
                        <input type="hidden" name="subtotal" id="subtotal" value="0">
                        <input type="hidden" name="tax" id="tax" value="0">
                        <input type="hidden" name="total" id="total" value="0">
                    </div>

                    <!-- Footer con acciones -->
                    <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3">
                        <a href="{{ route('admin.debit-notes.index') }}" 
                           class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Crear Nota de Débito
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let itemCount = 1;

        function loadInvoiceDetails(invoiceId) {
            const select = document.getElementById('invoice_id');
            const option = select.options[select.selectedIndex];
            const infoDiv = document.getElementById('invoiceInfo');
            
            if (invoiceId) {
                document.getElementById('customerName').textContent = option.dataset.customer;
                document.getElementById('invoiceTotal').textContent = '$' + parseInt(option.dataset.total).toLocaleString('es-CO');
                infoDiv.classList.remove('hidden');
            } else {
                infoDiv.classList.add('hidden');
            }
        }

        function toggleCustomReason(value) {
            const reasonField = document.getElementById('reason');
            const reasonMap = {
                'interest': 'Intereses por mora en el pago de la factura.',
                'adjustment': 'Ajuste de precio según acuerdo comercial.',
                'additional_service': 'Cargo por servicio adicional prestado.',
                'shipping': 'Cargo por envío o domicilio.',
                'other': ''
            };
            
            if (value && value !== 'other') {
                reasonField.value = reasonMap[value] || '';
            }
        }

        function addItem() {
            const container = document.getElementById('itemsContainer');
            const newItem = document.createElement('div');
            newItem.className = 'item-row flex gap-3 items-start p-3 bg-gray-50 rounded-lg';
            newItem.innerHTML = `
                <div class="flex-1">
                    <input type="text" name="items[${itemCount}][description]" required
                           class="w-full border-gray-300 rounded-lg text-sm"
                           placeholder="Descripción del concepto">
                </div>
                <div class="w-24">
                    <input type="number" name="items[${itemCount}][quantity]" value="1" min="1" required
                           class="w-full border-gray-300 rounded-lg text-sm text-center"
                           placeholder="Cant." onchange="calculateTotals()">
                </div>
                <div class="w-32">
                    <input type="number" name="items[${itemCount}][unit_price]" min="0" step="1" required
                           class="w-full border-gray-300 rounded-lg text-sm text-right"
                           placeholder="Valor" onchange="calculateTotals()">
                </div>
                <div class="w-32">
                    <input type="text" readonly
                           class="w-full bg-gray-100 border-gray-300 rounded-lg text-sm text-right item-subtotal"
                           value="$0">
                </div>
                <button type="button" onclick="removeItem(this)" 
                        class="text-red-500 hover:text-red-700 p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            `;
            container.appendChild(newItem);
            itemCount++;
            updateRemoveButtons();
        }

        function removeItem(button) {
            const row = button.closest('.item-row');
            row.remove();
            calculateTotals();
            updateRemoveButtons();
        }

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.item-row');
            rows.forEach((row, index) => {
                const btn = row.querySelector('button[onclick*="removeItem"]');
                if (rows.length === 1) {
                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            });
        }

        function calculateTotals() {
            let subtotal = 0;
            const rows = document.querySelectorAll('.item-row');
            
            rows.forEach(row => {
                const qty = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
                const price = parseFloat(row.querySelector('input[name*="[unit_price]"]').value) || 0;
                const itemSubtotal = qty * price;
                row.querySelector('.item-subtotal').value = '$' + itemSubtotal.toLocaleString('es-CO');
                subtotal += itemSubtotal;
            });

            const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
            const tax = subtotal * (taxRate / 100);
            const total = subtotal + tax;

            document.getElementById('subtotalDisplay').textContent = '$' + subtotal.toLocaleString('es-CO');
            document.getElementById('taxRateDisplay').textContent = taxRate;
            document.getElementById('taxDisplay').textContent = '$' + Math.round(tax).toLocaleString('es-CO');
            document.getElementById('totalDisplay').textContent = '$' + Math.round(total).toLocaleString('es-CO');

            document.getElementById('subtotal').value = subtotal;
            document.getElementById('tax').value = Math.round(tax);
            document.getElementById('total').value = Math.round(total);
        }

        // Inicializar si hay factura preseleccionada
        document.addEventListener('DOMContentLoaded', function() {
            const invoiceId = document.getElementById('invoice_id').value;
            if (invoiceId) {
                loadInvoiceDetails(invoiceId);
            }
        });
    </script>
</x-layouts.app>
