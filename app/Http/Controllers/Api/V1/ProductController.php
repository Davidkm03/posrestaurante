<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category'])
            ->where('is_active', true);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', $search);
            });
        }

        if ($request->boolean('pos_only')) {
            $query->where('show_in_pos', true);
        }

        $products = $query->orderBy('sort_order')->paginate($request->get('per_page', 50));

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load(['category', 'modifierGroups.modifiers']);

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    public function categories(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->where('show_in_pos', true)
            ->withCount(['products' => fn($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function byCategory(Category $category): JsonResponse
    {
        $products = $category->products()
            ->where('is_active', true)
            ->where('show_in_pos', true)
            ->with('modifierGroups.modifiers')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $products = Product::where('is_active', true)
            ->where('show_in_pos', true)
            ->where(function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->q}%")
                    ->orWhere('sku', 'like', "%{$request->q}%")
                    ->orWhere('barcode', $request->q);
            })
            ->with('category')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function barcode(string $barcode): JsonResponse
    {
        $product = Product::where('barcode', $barcode)
            ->where('is_active', true)
            ->with(['category', 'modifierGroups.modifiers'])
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }
}
