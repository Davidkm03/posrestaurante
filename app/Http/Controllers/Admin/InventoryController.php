<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Category;
use App\Enums\StockMovementType;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Dashboard de inventario
     */
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');

        // Productos con inventario activo
        $query = Product::where('track_inventory', true)
            ->where('is_active', true)
            ->with('category');

        // Filtros
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereRaw('stock <= min_stock');
            } elseif ($request->stock_status === 'out') {
                $query->where('stock', '<=', 0);
            } elseif ($request->stock_status === 'ok') {
                $query->whereRaw('stock > min_stock');
            }
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('sku', 'like', "%{$request->search}%");
            });
        }

        $products = $query->orderBy('name')->paginate(20);

        // Estadísticas
        $stats = [
            'total_products' => Product::where('track_inventory', true)->count(),
            'low_stock' => Product::where('track_inventory', true)->whereRaw('stock <= min_stock AND stock > 0')->count(),
            'out_of_stock' => Product::where('track_inventory', true)->where('stock', '<=', 0)->count(),
            'total_value' => Product::where('track_inventory', true)->selectRaw('SUM(stock * cost) as total')->value('total') ?? 0,
        ];

        $categories = Category::orderBy('name')->get();

        return view('admin.inventory.index', compact('products', 'stats', 'categories'));
    }

    /**
     * Ver detalle de producto con historial
     */
    public function show(Product $product)
    {
        $branchId = session('current_branch_id');

        $movements = StockMovement::where('product_id', $product->id)
            ->forBranch($branchId)
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.inventory.show', compact('product', 'movements'));
    }

    /**
     * Formulario de ajuste de inventario
     */
    public function adjustForm(Product $product)
    {
        return view('admin.inventory.adjust', compact('product'));
    }

    /**
     * Procesar ajuste de inventario
     */
    public function adjust(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|in:set,add,subtract',
            'quantity' => 'required|numeric|min:0',
            'notes' => 'required|string|max:500',
        ]);

        $branchId = session('current_branch_id');

        try {
            if ($request->type === 'set') {
                $this->inventoryService->adjustStock(
                    $branchId,
                    $product->id,
                    $request->quantity,
                    $request->notes
                );
            } else {
                $type = $request->type === 'add' 
                    ? StockMovementType::ADJUSTMENT_IN 
                    : StockMovementType::ADJUSTMENT_OUT;

                $this->inventoryService->recordMovement(
                    $branchId,
                    $product->id,
                    $type,
                    $request->quantity,
                    null,
                    $request->notes
                );
            }

            return redirect()
                ->route('admin.inventory.show', $product)
                ->with('success', 'Stock ajustado correctamente');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Registrar merma
     */
    public function waste(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'required|string|max:500',
        ]);

        $branchId = session('current_branch_id');

        try {
            $this->inventoryService->recordWaste(
                $branchId,
                $product->id,
                $request->quantity,
                $request->notes
            );

            return redirect()
                ->route('admin.inventory.show', $product)
                ->with('success', 'Merma registrada correctamente');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Ver todos los movimientos
     */
    public function movements(Request $request)
    {
        $branchId = session('current_branch_id');

        $query = StockMovement::forBranch($branchId)
            ->with(['product', 'user']);

        // Filtros
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $movements = $query->orderByDesc('created_at')->paginate(50);

        $products = Product::where('track_inventory', true)->orderBy('name')->get();
        $types = StockMovementType::cases();

        return view('admin.inventory.movements', compact('movements', 'products', 'types'));
    }

    /**
     * Productos con stock bajo
     */
    public function lowStock()
    {
        $products = $this->inventoryService->getLowStockProducts();

        return view('admin.inventory.low-stock', compact('products'));
    }

    /**
     * Valorización del inventario
     */
    public function valuation()
    {
        $categories = Product::where('track_inventory', true)
            ->where('is_active', true)
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(function ($products) {
                return [
                    'count' => $products->count(),
                    'total_stock' => $products->sum('stock'),
                    'cost_value' => $products->sum(fn($p) => $p->stock * $p->cost),
                    'sale_value' => $products->sum(fn($p) => $p->stock * $p->price),
                ];
            });

        $totals = [
            'products' => Product::where('track_inventory', true)->count(),
            'total_stock' => Product::where('track_inventory', true)->sum('stock'),
            'cost_value' => Product::where('track_inventory', true)->selectRaw('SUM(stock * cost) as total')->value('total') ?? 0,
            'sale_value' => Product::where('track_inventory', true)->selectRaw('SUM(stock * price) as total')->value('total') ?? 0,
        ];

        return view('admin.inventory.valuation', compact('categories', 'totals'));
    }
}
