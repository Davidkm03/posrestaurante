<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('document_number', 'like', "%{$request->search}%")
                    ->orWhere('contact_name', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $suppliers = $query->orderBy('name')->paginate(20);

        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document_type' => 'nullable|string|max:10',
            'document_number' => 'nullable|string|max:20',
            'verification_digit' => 'nullable|string|max:1',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'tax_regime' => 'nullable|string|max:30',
            'notes' => 'nullable|string|max:1000',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Supplier::create($validated);

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Proveedor creado exitosamente');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['purchaseOrders' => function ($q) {
            $q->latest()->limit(10);
        }]);

        return view('admin.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document_type' => 'nullable|string|max:10',
            'document_number' => 'nullable|string|max:20',
            'verification_digit' => 'nullable|string|max:1',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'tax_regime' => 'nullable|string|max:30',
            'notes' => 'nullable|string|max:1000',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $supplier->update($validated);

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Proveedor actualizado exitosamente');
    }

    public function destroy(Supplier $supplier)
    {
        // Verificar si tiene órdenes de compra
        if ($supplier->purchaseOrders()->exists()) {
            return back()->with('error', 'No se puede eliminar el proveedor porque tiene órdenes de compra asociadas');
        }

        $supplier->delete();

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Proveedor eliminado exitosamente');
    }
}
