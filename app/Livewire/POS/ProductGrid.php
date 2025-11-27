<?php

namespace App\Livewire\POS;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class ProductGrid extends Component
{
    use WithPagination;

    public ?int $categoryId = null;
    public string $search = '';
    public array $categories = [];
    public bool $showModifierModal = false;
    public ?int $selectedProductId = null;
    public array $selectedModifiers = [];
    public string $productNotes = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryId' => ['except' => null],
    ];

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = Category::where('is_active', true)
            ->withCount(['products' => fn($q) => $q->where('is_active', true)->where('show_in_pos', true)])
            ->orderBy('sort_order')
            ->get()
            ->toArray();
    }

    public function selectCategory(?int $categoryId)
    {
        $this->categoryId = $categoryId;
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function selectProduct(int $productId)
    {
        $product = Product::with('modifierGroups.modifiers')->find($productId);

        if (!$product) return;

        // If product has modifiers, show modal
        if ($product->modifierGroups->isNotEmpty()) {
            $this->selectedProductId = $productId;
            $this->selectedModifiers = [];
            $this->productNotes = '';
            $this->showModifierModal = true;
        } else {
            // Add directly to cart
            $this->dispatch('add-product', productId: $productId, modifiers: [], notes: '');
        }
    }

    public function toggleModifier(int $modifierId, int $groupId, bool $isMultiple)
    {
        if ($isMultiple) {
            // Multiple selection (checkbox)
            if (in_array($modifierId, $this->selectedModifiers)) {
                $this->selectedModifiers = array_values(array_diff($this->selectedModifiers, [$modifierId]));
            } else {
                $this->selectedModifiers[] = $modifierId;
            }
        } else {
            // Single selection (radio) - remove others from same group
            $product = Product::with('modifierGroups.modifiers')->find($this->selectedProductId);
            $group = $product->modifierGroups->find($groupId);

            if ($group) {
                $groupModifierIds = $group->modifiers->pluck('id')->toArray();
                $this->selectedModifiers = array_values(array_diff($this->selectedModifiers, $groupModifierIds));
                $this->selectedModifiers[] = $modifierId;
            }
        }
    }

    public function confirmProductWithModifiers()
    {
        if (!$this->selectedProductId) return;

        $this->dispatch('add-product',
            productId: $this->selectedProductId,
            modifiers: $this->selectedModifiers,
            notes: $this->productNotes
        );

        $this->closeModifierModal();
    }

    public function closeModifierModal()
    {
        $this->showModifierModal = false;
        $this->selectedProductId = null;
        $this->selectedModifiers = [];
        $this->productNotes = '';
    }

    public function scanBarcode(string $barcode)
    {
        $product = Product::where('barcode', $barcode)
            ->where('is_active', true)
            ->where('show_in_pos', true)
            ->first();

        if ($product) {
            $this->selectProduct($product->id);
        } else {
            $this->dispatch('notify', type: 'error', message: 'Producto no encontrado');
        }
    }

    public function render()
    {
        $query = Product::where('is_active', true)
            ->where('show_in_pos', true)
            ->with(['category', 'modifierGroups']);

        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('sku', 'like', "%{$this->search}%")
                  ->orWhere('barcode', 'like', "%{$this->search}%");
            });
        }

        $products = $query->orderBy('name')->paginate(20);

        $selectedProduct = null;
        if ($this->selectedProductId) {
            $selectedProduct = Product::with('modifierGroups.modifiers')->find($this->selectedProductId);
        }

        return view('livewire.pos.product-grid', [
            'products' => $products,
            'selectedProduct' => $selectedProduct,
        ]);
    }
}
