<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Printer;
use App\Models\Order;
use App\Models\CashSession;
use App\Services\PrinterService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PrintController extends Controller
{
    protected PrinterService $printerService;

    public function __construct(PrinterService $printerService)
    {
        $this->printerService = $printerService;
    }

    /**
     * Lista de impresoras configuradas
     */
    public function index()
    {
        $branchId = session('current_branch_id');
        $printers = Printer::with('branch')
            ->when($branchId, fn($q) => $q->forBranch($branchId))
            ->orderBy('type')
            ->get();
        
        $branches = Branch::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.settings.printers', compact('printers', 'branches', 'categories'));
    }

    /**
     * Formulario para crear impresora
     */
    public function create()
    {
        return view('admin.settings.printers.create');
    }

    /**
     * Guardar nueva impresora
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'branch_id' => 'nullable|exists:branches,id',
            'type' => 'required|in:receipt,kitchen,bar,label',
            'connection_type' => 'required|in:network,usb,bluetooth',
            'ip_address' => 'nullable|ip|required_if:connection_type,network',
            'port' => 'nullable|integer|min:1|max:65535',
            'device_path' => 'nullable|string|max:255',
            'paper_width' => 'required|in:58,80',
            'auto_cut' => 'boolean',
            'open_drawer' => 'boolean',
            'is_active' => 'boolean',
            'categories' => 'nullable|array',
            'categories.*' => 'integer|exists:categories,id',
        ]);

        $validated['branch_id'] = $validated['branch_id'] ?? session('current_branch_id');
        $validated['port'] = $validated['port'] ?? 9100;
        $validated['auto_cut'] = $request->boolean('auto_cut', true);
        $validated['open_drawer'] = $request->boolean('open_drawer');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['categories'] = $validated['categories'] ?? [];

        Printer::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Impresora agregada correctamente']);
        }

        return redirect()
            ->route('admin.settings.printers')
            ->with('success', 'Impresora configurada correctamente');
    }

    /**
     * Actualizar impresora
     */
    public function update(Request $request, Printer $printer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'branch_id' => 'nullable|exists:branches,id',
            'type' => 'required|in:receipt,kitchen,bar,label',
            'connection_type' => 'required|in:network,usb,bluetooth',
            'ip_address' => 'nullable|ip|required_if:connection_type,network',
            'port' => 'nullable|integer|min:1|max:65535',
            'device_path' => 'nullable|string|max:255',
            'paper_width' => 'required|in:58,80',
            'auto_cut' => 'boolean',
            'open_drawer' => 'boolean',
            'is_active' => 'boolean',
            'categories' => 'nullable|array',
            'categories.*' => 'integer|exists:categories,id',
        ]);

        $validated['auto_cut'] = $request->boolean('auto_cut', true);
        $validated['open_drawer'] = $request->boolean('open_drawer');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['categories'] = $validated['categories'] ?? [];

        $printer->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Impresora actualizada']);
        }

        return redirect()
            ->route('admin.settings.printers')
            ->with('success', 'Impresora actualizada correctamente');
    }

    /**
     * Eliminar impresora
     */
    public function destroy(Printer $printer)
    {
        $printer->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Impresora eliminada']);
        }

        return redirect()
            ->route('admin.settings.printers')
            ->with('success', 'Impresora eliminada correctamente');
    }

    /**
     * Probar conexión con impresora
     */
    public function test(Printer $printer): JsonResponse
    {
        try {
            if ($printer->connection_type === 'network') {
                $service = new PrinterService('escpos', $printer->ip_address, $printer->port);
                $service->setPaperWidth($printer->paper_width);
                
                $testData = $service->generateTestTicket();
                $service->printToNetwork($testData);
                
                return response()->json([
                    'success' => true, 
                    'message' => 'Impresión de prueba enviada correctamente'
                ]);
            }

            // Para USB/Bluetooth, devolver datos para imprimir desde el navegador
            $service = new PrinterService('escpos');
            $service->setPaperWidth($printer->paper_width);
            
            return response()->json([
                'success' => true,
                'type' => $printer->connection_type,
                'data' => base64_encode($service->generateTestTicket()),
                'message' => 'Datos listos para enviar a la impresora'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Imprimir ticket de orden
     */
    public function printReceipt(Order $order): JsonResponse
    {
        $order->load(['items.product', 'items.modifiers', 'table', 'waiter', 'branch', 'payments.paymentMethod']);
        
        $printer = Printer::getForType('receipt', $order->branch_id);
        
        $this->printerService->setPaperWidth($printer?->paper_width ?? 80);
        $data = $this->printerService->generateReceipt($order);
        
        if ($printer && $printer->connection_type === 'network') {
            try {
                $service = new PrinterService('escpos', $printer->ip_address, $printer->port);
                $service->printToNetwork($data);
                
                return response()->json(['success' => true, 'message' => 'Ticket enviado a impresora']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
        }
        
        // Devolver datos para imprimir desde el navegador
        return response()->json([
            'success' => true,
            'type' => $printer?->connection_type ?? 'browser',
            'data' => base64_encode($data),
        ]);
    }

    /**
     * Imprimir comanda de cocina
     */
    public function printKitchenTicket(Order $order, ?string $destination = 'kitchen'): JsonResponse
    {
        $order->load(['items.product', 'items.modifiers', 'table', 'waiter']);
        
        $printer = Printer::getForType($destination, $order->branch_id);
        
        $this->printerService->setPaperWidth($printer?->paper_width ?? 80);
        $data = $this->printerService->generateKitchenTicket($order, $destination);
        
        if ($printer && $printer->connection_type === 'network') {
            try {
                $service = new PrinterService('escpos', $printer->ip_address, $printer->port);
                $service->printToNetwork($data);
                
                return response()->json(['success' => true, 'message' => 'Comanda enviada a ' . $destination]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
        }
        
        return response()->json([
            'success' => true,
            'type' => $printer?->connection_type ?? 'browser',
            'data' => base64_encode($data),
        ]);
    }

    /**
     * Imprimir cierre de caja
     */
    public function printCashClose(CashSession $session): JsonResponse
    {
        $session->load(['cashRegister', 'user']);
        
        $data = $this->printerService->generateCashCloseTicket($session);
        
        return response()->json([
            'success' => true,
            'type' => 'browser',
            'data' => base64_encode($data),
        ]);
    }

    /**
     * Obtener impresoras por tipo (API)
     */
    public function getByType(string $type): JsonResponse
    {
        $branchId = session('current_branch_id');
        $printers = Printer::active()
            ->forBranch($branchId)
            ->forType($type)
            ->get();

        return response()->json($printers);
    }
}
