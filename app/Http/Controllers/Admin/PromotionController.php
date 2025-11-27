<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Category;
use App\Models\Product;
use App\Services\PromotionService;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    protected PromotionService $promotionService;

    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    public function index(Request $request)
    {
        $branchId = session('current_branch_id');
        
        $query = Promotion::where('branch_id', $branchId)
            ->withCount('usages');

        // Filtros
        if ($request->filled('status')) {
            $query->when($request->status === 'active', fn($q) => $q->where('is_active', true))
                  ->when($request->status === 'inactive', fn($q) => $q->where('is_active', false))
                  ->when($request->status === 'expired', fn($q) => $q->where('ends_at', '<', now()));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('coupon_code', 'like', '%' . $request->search . '%');
            });
        }

        $promotions = $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Stats
        $stats = $this->promotionService->getStats($branchId);

        return view('admin.promotions.index', compact('promotions', 'stats'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('admin.promotions.create', compact('categories', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed,buy_x_get_y,bundle,happy_hour,free_delivery',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'buy_quantity' => 'nullable|integer|min:1',
            'get_quantity' => 'nullable|integer|min:1',
            'applicable_products' => 'nullable|array',
            'applicable_products.*' => 'exists:products,id',
            'applicable_categories' => 'nullable|array',
            'applicable_categories.*' => 'exists:categories,id',
            'applicable_days' => 'nullable|array',
            'applicable_days.*' => 'integer|between:0,6',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_customer' => 'nullable|integer|min:1',
            'coupon_code' => 'nullable|string|max:50|unique:promotions,coupon_code',
            'is_active' => 'boolean',
            'priority' => 'integer|min:0',
            'is_combinable' => 'boolean',
        ]);

        $validated['branch_id'] = session('current_branch_id');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_combinable'] = $request->boolean('is_combinable');

        // Limpiar arrays vacíos
        if (empty($validated['applicable_products'])) {
            $validated['applicable_products'] = null;
        }
        if (empty($validated['applicable_categories'])) {
            $validated['applicable_categories'] = null;
        }
        if (empty($validated['applicable_days'])) {
            $validated['applicable_days'] = null;
        }

        $promotion = $this->promotionService->create($validated);

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Promoción creada correctamente');
    }

    public function show(Promotion $promotion)
    {
        $promotion->load('usages.order', 'usages.customer');
        
        $usageStats = [
            'total_uses' => $promotion->usages->count(),
            'total_discount' => $promotion->usages->sum('discount_amount'),
            'unique_customers' => $promotion->usages->pluck('customer_id')->filter()->unique()->count(),
        ];

        return view('admin.promotions.show', compact('promotion', 'usageStats'));
    }

    public function edit(Promotion $promotion)
    {
        $categories = Category::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('admin.promotions.edit', compact('promotion', 'categories', 'products'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed,buy_x_get_y,bundle,happy_hour,free_delivery',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'buy_quantity' => 'nullable|integer|min:1',
            'get_quantity' => 'nullable|integer|min:1',
            'applicable_products' => 'nullable|array',
            'applicable_products.*' => 'exists:products,id',
            'applicable_categories' => 'nullable|array',
            'applicable_categories.*' => 'exists:categories,id',
            'applicable_days' => 'nullable|array',
            'applicable_days.*' => 'integer|between:0,6',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_customer' => 'nullable|integer|min:1',
            'coupon_code' => 'nullable|string|max:50|unique:promotions,coupon_code,' . $promotion->id,
            'is_active' => 'boolean',
            'priority' => 'integer|min:0',
            'is_combinable' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_combinable'] = $request->boolean('is_combinable');

        // Limpiar arrays vacíos
        if (empty($validated['applicable_products'])) {
            $validated['applicable_products'] = null;
        }
        if (empty($validated['applicable_categories'])) {
            $validated['applicable_categories'] = null;
        }
        if (empty($validated['applicable_days'])) {
            $validated['applicable_days'] = null;
        }

        $this->promotionService->update($promotion, $validated);

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Promoción actualizada correctamente');
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Promoción eliminada correctamente');
    }

    public function toggleActive(Promotion $promotion)
    {
        $promotion->update(['is_active' => !$promotion->is_active]);

        return back()->with('success', 
            $promotion->is_active ? 'Promoción activada' : 'Promoción desactivada'
        );
    }

    public function duplicate(Promotion $promotion)
    {
        $newPromotion = $this->promotionService->duplicate($promotion);

        return redirect()
            ->route('admin.promotions.edit', $newPromotion)
            ->with('success', 'Promoción duplicada. Modifica los detalles según necesites.');
    }
}
