@extends('layouts.admin')

@section('title', 'Configuración de Impresoras')

@section('header-actions')
    <button onclick="openPrinterModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Nueva Impresora
    </button>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Lista de Impresoras -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Conexión</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Destino</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Papel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="printers-list">
                    @forelse($printers as $printer)
                    <tr data-printer-id="{{ $printer->id }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $printer->name }}</div>
                            @if($printer->branch)
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $printer->branch->name }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $printer->type === 'receipt' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                {{ $printer->type === 'kitchen' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}
                                {{ $printer->type === 'bar' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                {{ $printer->type === 'label' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}">
                                {{ ucfirst($printer->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            @if($printer->connection_type === 'network')
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                    </svg>
                                    Red
                                </span>
                            @elseif($printer->connection_type === 'usb')
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    USB
                                </span>
                            @else
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
                                    </svg>
                                    Bluetooth
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            @if($printer->connection_type === 'network')
                                {{ $printer->ip_address }}:{{ $printer->port }}
                            @else
                                {{ $printer->device_path ?? 'No configurado' }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $printer->paper_width }}mm
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($printer->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Activa
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    Inactiva
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="testPrinter({{ $printer->id }})" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 mr-3" title="Probar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </button>
                            <button onclick="editPrinter({{ $printer->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3" title="Editar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="deletePrinter({{ $printer->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Eliminar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr id="no-printers-row">
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay impresoras configuradas</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comienza agregando una nueva impresora.</p>
                            <div class="mt-6">
                                <button onclick="openPrinterModal()" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Nueva Impresora
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Información de ayuda -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Tipos de conexión</h3>
                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                    <ul class="list-disc pl-5 space-y-1">
                        <li><strong>Red:</strong> Impresoras conectadas via Ethernet o WiFi. Requiere IP y puerto (default: 9100)</li>
                        <li><strong>USB:</strong> Impresoras conectadas directamente al servidor o dispositivo</li>
                        <li><strong>Bluetooth:</strong> Impresoras portátiles conectadas via Bluetooth</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Impresora -->
<div id="printer-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
        <form id="printer-form" onsubmit="savePrinter(event)">
            <input type="hidden" id="printer-id" name="id">
            
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 id="modal-title" class="text-lg font-medium text-gray-900 dark:text-white">Nueva Impresora</h3>
            </div>
            
            <div class="px-6 py-4 space-y-4">
                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre *</label>
                    <input type="text" id="printer-name" name="name" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                        placeholder="Ej: Cocina Principal">
                </div>
                
                <!-- Sucursal -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sucursal</label>
                    <select id="printer-branch" name="branch_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="">Todas las sucursales</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tipo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Impresora *</label>
                    <select id="printer-type" name="type" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="receipt">Recibos / Facturas</option>
                        <option value="kitchen">Cocina</option>
                        <option value="bar">Bar</option>
                        <option value="label">Etiquetas</option>
                    </select>
                </div>

                <!-- Tipo de Conexión -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Conexión *</label>
                    <select id="printer-connection" name="connection_type" required onchange="toggleConnectionFields()"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="network">Red (Ethernet/WiFi)</option>
                        <option value="usb">USB</option>
                        <option value="bluetooth">Bluetooth</option>
                    </select>
                </div>

                <!-- Campos de Red -->
                <div id="network-fields">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dirección IP *</label>
                            <input type="text" id="printer-ip" name="ip_address"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                                placeholder="192.168.1.100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Puerto</label>
                            <input type="number" id="printer-port" name="port" value="9100"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Campos de USB/Bluetooth -->
                <div id="device-fields" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ruta del Dispositivo</label>
                    <input type="text" id="printer-device" name="device_path"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                        placeholder="/dev/usb/lp0 o dirección MAC">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Para USB: /dev/usb/lp0, Para Bluetooth: AA:BB:CC:DD:EE:FF</p>
                </div>

                <!-- Ancho de Papel -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ancho de Papel</label>
                    <select id="printer-paper" name="paper_width"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="58">58mm (Mini)</option>
                        <option value="80" selected>80mm (Standard)</option>
                    </select>
                </div>

                <!-- Categorías (para cocina/bar) -->
                <div id="categories-field">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categorías a Imprimir</label>
                    <div class="max-h-32 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-lg p-2">
                        @foreach($categories as $category)
                        <label class="flex items-center py-1">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $category->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Deja vacío para imprimir todas las categorías</p>
                </div>

                <!-- Opciones adicionales -->
                <div class="flex items-center space-x-6">
                    <label class="flex items-center">
                        <input type="checkbox" id="printer-autocut" name="auto_cut" checked
                            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Corte automático</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" id="printer-drawer" name="open_drawer"
                            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Abrir cajón</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" id="printer-active" name="is_active" checked
                            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Activa</span>
                    </label>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button type="button" onclick="closePrinterModal()" 
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancelar
                </button>
                <button type="submit" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const printersData = @json($printers);
    
    function openPrinterModal(printer = null) {
        const modal = document.getElementById('printer-modal');
        const title = document.getElementById('modal-title');
        const form = document.getElementById('printer-form');
        
        form.reset();
        document.getElementById('printer-id').value = '';
        
        if (printer) {
            title.textContent = 'Editar Impresora';
            document.getElementById('printer-id').value = printer.id;
            document.getElementById('printer-name').value = printer.name;
            document.getElementById('printer-branch').value = printer.branch_id || '';
            document.getElementById('printer-type').value = printer.type;
            document.getElementById('printer-connection').value = printer.connection_type;
            document.getElementById('printer-ip').value = printer.ip_address || '';
            document.getElementById('printer-port').value = printer.port || 9100;
            document.getElementById('printer-device').value = printer.device_path || '';
            document.getElementById('printer-paper').value = printer.paper_width;
            document.getElementById('printer-autocut').checked = printer.auto_cut;
            document.getElementById('printer-drawer').checked = printer.open_drawer;
            document.getElementById('printer-active').checked = printer.is_active;
            
            // Marcar categorías
            if (printer.categories && Array.isArray(printer.categories)) {
                document.querySelectorAll('input[name="categories[]"]').forEach(cb => {
                    cb.checked = printer.categories.includes(parseInt(cb.value));
                });
            }
        } else {
            title.textContent = 'Nueva Impresora';
        }
        
        toggleConnectionFields();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    function closePrinterModal() {
        const modal = document.getElementById('printer-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    function toggleConnectionFields() {
        const connectionType = document.getElementById('printer-connection').value;
        const networkFields = document.getElementById('network-fields');
        const deviceFields = document.getElementById('device-fields');
        
        if (connectionType === 'network') {
            networkFields.classList.remove('hidden');
            deviceFields.classList.add('hidden');
            document.getElementById('printer-ip').required = true;
        } else {
            networkFields.classList.add('hidden');
            deviceFields.classList.remove('hidden');
            document.getElementById('printer-ip').required = false;
        }
    }
    
    function editPrinter(printerId) {
        const printer = printersData.find(p => p.id === printerId);
        if (printer) {
            openPrinterModal(printer);
        }
    }
    
    async function savePrinter(event) {
        event.preventDefault();
        
        const form = document.getElementById('printer-form');
        const formData = new FormData(form);
        const printerId = formData.get('id');
        
        // Construir objeto de datos
        const data = {
            name: formData.get('name'),
            branch_id: formData.get('branch_id') || null,
            type: formData.get('type'),
            connection_type: formData.get('connection_type'),
            ip_address: formData.get('ip_address') || null,
            port: parseInt(formData.get('port')) || 9100,
            device_path: formData.get('device_path') || null,
            paper_width: parseInt(formData.get('paper_width')),
            auto_cut: formData.has('auto_cut'),
            open_drawer: formData.has('open_drawer'),
            is_active: formData.has('is_active'),
            categories: formData.getAll('categories[]').map(Number)
        };
        
        try {
            let url = '/admin/settings/printers';
            let method = 'POST';
            
            if (printerId) {
                url = '/admin/settings/printers/' + printerId;
                method = 'PUT';
            }
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification(result.message, 'success');
                closePrinterModal();
                location.reload();
            } else {
                showNotification(result.message || 'Error al guardar', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error al guardar la impresora', 'error');
        }
    }
    
    async function deletePrinter(printerId) {
        if (!confirm('¿Estás seguro de eliminar esta impresora?')) {
            return;
        }
        
        try {
            const response = await fetch('/admin/settings/printers/' + printerId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification(result.message, 'success');
                document.querySelector(`tr[data-printer-id="${printerId}"]`)?.remove();
                
                // Mostrar mensaje vacío si no hay más impresoras
                const tbody = document.getElementById('printers-list');
                if (tbody.children.length === 0) {
                    location.reload();
                }
            } else {
                showNotification(result.message || 'Error al eliminar', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error al eliminar la impresora', 'error');
        }
    }
    
    async function testPrinter(printerId) {
        try {
            showNotification('Enviando prueba de impresión...', 'info');
            
            const response = await fetch('/admin/settings/printers/' + printerId + '/test', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification(result.message, 'success');
            } else {
                showNotification(result.message || 'Error en la prueba', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error al probar la impresora', 'error');
        }
    }
    
    function showNotification(message, type = 'info') {
        // Usar el sistema de notificaciones existente si está disponible
        if (typeof Livewire !== 'undefined') {
            Livewire.dispatch('notify', { message: message, type: type });
        } else {
            alert(message);
        }
    }
    
    // Cerrar modal con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePrinterModal();
        }
    });
</script>
@endpush
