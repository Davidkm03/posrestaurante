<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::withCount('products');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $categories = $query->orderBy('sort_order')->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'show_in_pos' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['show_in_pos'] = $request->boolean('show_in_pos', true);
        $validated['sort_order'] = Category::max('sort_order') + 1;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'show_in_pos' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['show_in_pos'] = $request->boolean('show_in_pos');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'No se puede eliminar la categoría porque tiene productos asociados.');
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Categoría eliminada exitosamente.');
    }

    public function toggleStatus(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        return back()->with('success', 'Estado de la categoría actualizado.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:categories,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            Category::where('id', $item['id'])->update(['sort_order' => $item['order']]);
        }

        return response()->json(['message' => 'Orden actualizado']);
    }
}
