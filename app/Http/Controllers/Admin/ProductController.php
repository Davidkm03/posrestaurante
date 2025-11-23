<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ModifierGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $products = $query->orderBy('sort_order')->paginate(20);
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $modifierGroups = ModifierGroup::orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'modifierGroups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|max:50|unique:products,sku',
            'barcode' => 'nullable|string|max:50|unique:products,barcode',
            'tax_type' => 'required|string|in:01,04,ZZ',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'tax_included' => 'boolean',
            'is_active' => 'boolean',
            'show_in_pos' => 'boolean',
            'allow_notes' => 'boolean',
            'image' => 'nullable|image|max:2048',
            'modifier_groups' => 'nullable|array',
            'modifier_groups.*' => 'exists:modifier_groups,id',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['show_in_pos'] = $request->boolean('show_in_pos', true);
        $validated['allow_notes'] = $request->boolean('allow_notes', true);
        $validated['tax_included'] = $request->boolean('tax_included', true);

        // Manejar imagen
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        // Asignar grupos de modificadores
        if ($request->filled('modifier_groups')) {
            $product->modifierGroups()->sync($request->modifier_groups);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $modifierGroups = ModifierGroup::orderBy('name')->get();
        $product->load('modifierGroups');

        return view('admin.products.edit', compact('product', 'categories', 'modifierGroups'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'sku' => ['nullable', 'string', 'max:50', Rule::unique('products')->ignore($product->id)],
            'barcode' => ['nullable', 'string', 'max:50', Rule::unique('products')->ignore($product->id)],
            'tax_type' => 'required|string|in:01,04,ZZ',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'tax_included' => 'boolean',
            'is_active' => 'boolean',
            'show_in_pos' => 'boolean',
            'allow_notes' => 'boolean',
            'image' => 'nullable|image|max:2048',
            'modifier_groups' => 'nullable|array',
            'modifier_groups.*' => 'exists:modifier_groups,id',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['show_in_pos'] = $request->boolean('show_in_pos');
        $validated['allow_notes'] = $request->boolean('allow_notes');
        $validated['tax_included'] = $request->boolean('tax_included');

        // Manejar imagen
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        // Sincronizar grupos de modificadores
        $product->modifierGroups()->sync($request->modifier_groups ?? []);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product)
    {
        // Verificar si tiene órdenes asociadas
        if ($product->orderItems()->exists()) {
            return back()->with('error', 'No se puede eliminar el producto porque tiene órdenes asociadas.');
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        return back()->with('success', 'Estado del producto actualizado.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            Product::where('id', $item['id'])->update(['sort_order' => $item['order']]);
        }

        return response()->json(['message' => 'Orden actualizado']);
    }
}
