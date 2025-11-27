<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');

        $query = PurchaseOrder::where('branch_id', $branchId)
            ->with(['supplier', 'user']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")
                    ->orWhereHas('supplier', function ($sq) use ($request) {
                        $sq->where('name', 'like', "%{$request->search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $purchases = $query->orderByDesc('order_date')->paginate(20);
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('admin.purchases.index', compact('purchases', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $ingredients = Ingredient::active()->orderBy('name')->get();

        return view('admin.purchases.create', compact('suppliers', 'ingredients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.ingredient_id' => 'required|exists:ingredients,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.unit' => 'required|string|max:20',
        ]);

        $branchId = session('current_branch_id');

        DB::transaction(function () use ($validated, $branchId) {
            $purchase = PurchaseOrder::create([
                'branch_id' => $branchId,
                'supplier_id' => $validated['supplier_id'],
                'user_id' => auth()->id(),
                'order_number' => PurchaseOrder::generateOrderNumber(),
                'status' => 'draft',
                'order_date' => $validated['order_date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'subtotal' => 0,
                'tax_amount' => 0,
                'total' => 0,
            ]);

            foreach ($validated['items'] as $item) {
                $total = $item['quantity'] * $item['unit_cost'];
                
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchase->id,
                    'ingredient_id' => $item['ingredient_id'],
                    'quantity_ordered' => $item['quantity'],
                    'quantity_received' => 0,
                    'unit_cost' => $item['unit_cost'],
                    'total' => $total,
                    'unit' => $item['unit'],
                ]);
            }

            $purchase->calculateTotals();
        });

        return redirect()
            ->route('admin.purchases.index')
            ->with('success', 'Orden de compra creada exitosamente');
    }

    public function show(PurchaseOrder $purchase)
    {
        $purchase->load(['supplier', 'user', 'items.ingredient']);

        return view('admin.purchases.show', compact('purchase'));
    }

    public function edit(PurchaseOrder $purchase)
    {
        if (!in_array($purchase->status, ['draft'])) {
            return back()->with('error', 'Solo se pueden editar órdenes en borrador');
        }

        $purchase->load('items.ingredient');
        $suppliers = Supplier::active()->orderBy('name')->get();
        $ingredients = Ingredient::active()->orderBy('name')->get();

        return view('admin.purchases.edit', compact('purchase', 'suppliers', 'ingredients'));
    }

    public function update(Request $request, PurchaseOrder $purchase)
    {
        if (!in_array($purchase->status, ['draft'])) {
            return back()->with('error', 'Solo se pueden editar órdenes en borrador');
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.ingredient_id' => 'required|exists:ingredients,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.unit' => 'required|string|max:20',
        ]);

        DB::transaction(function () use ($validated, $purchase) {
            $purchase->update([
                'supplier_id' => $validated['supplier_id'],
                'order_date' => $validated['order_date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Eliminar items existentes y recrear
            $purchase->items()->delete();

            foreach ($validated['items'] as $item) {
                $total = $item['quantity'] * $item['unit_cost'];
                
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchase->id,
                    'ingredient_id' => $item['ingredient_id'],
                    'quantity_ordered' => $item['quantity'],
                    'quantity_received' => 0,
                    'unit_cost' => $item['unit_cost'],
                    'total' => $total,
                    'unit' => $item['unit'],
                ]);
            }

            $purchase->calculateTotals();
        });

        return redirect()
            ->route('admin.purchases.show', $purchase)
            ->with('success', 'Orden de compra actualizada exitosamente');
    }

    public function destroy(PurchaseOrder $purchase)
    {
        if (!in_array($purchase->status, ['draft', 'cancelled'])) {
            return back()->with('error', 'Solo se pueden eliminar órdenes en borrador o canceladas');
        }

        $purchase->delete();

        return redirect()
            ->route('admin.purchases.index')
            ->with('success', 'Orden de compra eliminada exitosamente');
    }

    /**
     * Recibir orden de compra (actualiza inventario)
     */
    public function receive(Request $request, PurchaseOrder $purchase)
    {
        if (!$purchase->canBeReceived()) {
            return back()->with('error', 'Esta orden no puede ser recibida');
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity_received' => 'required|numeric|min:0',
            'supplier_invoice' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($validated, $purchase) {
            $allComplete = true;

            foreach ($validated['items'] as $itemData) {
                $item = PurchaseOrderItem::find($itemData['id']);
                
                if ($itemData['quantity_received'] > 0) {
                    $item->receiveQuantity($itemData['quantity_received']);

                    // Actualizar stock del ingrediente
                    $ingredient = $item->ingredient;
                    $ingredient->stock += $itemData['quantity_received'];
                    $ingredient->unit_cost = $item->unit_cost; // Actualizar costo
                    $ingredient->save();
                }

                if (!$item->is_complete) {
                    $allComplete = false;
                }
            }

            $purchase->update([
                'status' => $allComplete ? 'received' : 'partial',
                'received_date' => $allComplete ? now() : null,
                'supplier_invoice' => $validated['supplier_invoice'] ?? $purchase->supplier_invoice,
            ]);
        });

        return redirect()
            ->route('admin.purchases.show', $purchase)
            ->with('success', 'Mercancía recibida exitosamente');
    }

    /**
     * Cambiar estado de la orden
     */
    public function updateStatus(Request $request, PurchaseOrder $purchase)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,sent,confirmed,cancelled',
        ]);

        $purchase->update(['status' => $validated['status']]);

        return back()->with('success', 'Estado actualizado');
    }
}
