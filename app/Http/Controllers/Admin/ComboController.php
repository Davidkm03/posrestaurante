<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ComboItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ComboController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $combos = Product::where('is_combo', true)
            ->with(['category', 'comboProducts'])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.combos.index', compact('combos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $products = Product::where('is_active', true)
            ->where('is_combo', false) // No incluir otros combos
            ->orderBy('name')
            ->get();

        return view('admin.combos.create', compact('categories', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'products' => 'required|array|min:2',
            'products.*' => 'exists:products,id',
            'is_active' => 'nullable',
            'show_in_pos' => 'nullable',
        ]);

        // Crear el producto como combo
        $combo = Product::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'category_id' => $validated['category_id'],
            'is_combo' => true,
            'is_active' => $request->has('is_active'),
            'show_in_pos' => $request->has('show_in_pos'),
            'tax_type' => '01', // IVA
            'tax_percentage' => 19,
            'tax_included' => true,
        ]);

        // Agregar los productos al combo
        foreach ($validated['products'] as $index => $productId) {
            ComboItem::create([
                'combo_id' => $combo->id,
                'product_id' => $productId,
                'quantity' => 1,
                'sort_order' => $index,
            ]);
        }

        return redirect()->route('admin.combos.index')
            ->with('success', 'Combo "' . $combo->name . '" creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $combo = Product::where('is_combo', true)
            ->with(['category', 'comboProducts'])
            ->findOrFail($id);

        return view('admin.combos.show', compact('combo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $combo = Product::where('is_combo', true)
            ->with('comboProducts')
            ->findOrFail($id);

        $categories = Category::orderBy('name')->get();
        $products = Product::where('is_active', true)
            ->where('is_combo', false)
            ->orderBy('name')
            ->get();

        // IDs de productos ya en el combo
        $selectedProducts = $combo->comboProducts->pluck('id')->toArray();

        return view('admin.combos.edit', compact('combo', 'categories', 'products', 'selectedProducts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $combo = Product::where('is_combo', true)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'products' => 'required|array|min:2',
            'products.*' => 'exists:products,id',
            'is_active' => 'nullable',
            'show_in_pos' => 'nullable',
        ]);

        // Actualizar el combo
        $combo->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'category_id' => $validated['category_id'],
            'is_active' => $request->has('is_active'),
            'show_in_pos' => $request->has('show_in_pos'),
        ]);

        // Actualizar los productos del combo
        ComboItem::where('combo_id', $combo->id)->delete();
        
        foreach ($validated['products'] as $index => $productId) {
            ComboItem::create([
                'combo_id' => $combo->id,
                'product_id' => $productId,
                'quantity' => 1,
                'sort_order' => $index,
            ]);
        }

        return redirect()->route('admin.combos.index')
            ->with('success', 'Combo "' . $combo->name . '" actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $combo = Product::where('is_combo', true)->findOrFail($id);
        $name = $combo->name;

        // Eliminar items del combo
        ComboItem::where('combo_id', $combo->id)->delete();
        
        // Eliminar el combo (soft delete)
        $combo->delete();

        return redirect()->route('admin.combos.index')
            ->with('success', 'Combo "' . $name . '" eliminado exitosamente');
    }
}
