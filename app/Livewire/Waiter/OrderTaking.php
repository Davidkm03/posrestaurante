<?php

namespace App\Livewire\Waiter;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class OrderTaking extends Component
{
    public Table $table;
    public ?Order $order = null;
    public $categories;
    public $products = [];
    public $selectedCategory = null;
    public $cart = [];
    public $guests = 1;
    public $notes = '';
    public $searchTerm = '';
    
    // Modifier selection
    public $showModifierModal = false;
    public $selectedProduct = null;
    public $selectedModifiers = [];
    public $modifierQuantity = 1;
    public $modifierNotes = '';

    protected $listeners = ['productAdded' => '$refresh'];

    public function mount(Table $table)
    {
        $this->table = $table->load('zone', 'currentOrder.items.product');
        
        // Si la mesa tiene una orden activa, cargarla
        if ($this->table->currentOrder) {
            $this->order = $this->table->currentOrder;
            $this->guests = $this->order->guests;
            $this->notes = $this->order->notes ?? '';
            
            // Cargar items existentes en el cart
            foreach ($this->order->items as $item) {
                $this->cart[] = [
                    'id' => uniqid(),
                    'product_id' => $item->product_id,
                    'name' => $item->name,
                    'price' => (float) $item->unit_price,
                    'quantity' => (int) $item->quantity,
                    'notes' => $item->notes ?? '',
                    'modifiers' => [],
                    'existing_item_id' => $item->id, // Para identificar items ya guardados
                ];
            }
        }

        // Cargar categorías
        $this->categories = Category::where('is_active', true)
            ->where('show_in_pos', true)
            ->withCount(['products' => function ($query) {
                $query->where('is_active', true)->where('show_in_pos', true);
            }])
            ->orderBy('sort_order')
            ->get();

        // Seleccionar primera categoría por defecto
        if ($this->categories->isNotEmpty() && !$this->selectedCategory) {
            $this->selectedCategory = $this->categories->first()->id;
        }

        $this->loadProducts();
    }

    public function loadProducts()
    {
        $query = Product::where('is_active', true)
            ->where('show_in_pos', true);

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('sku', 'like', '%' . $this->searchTerm . '%');
            });
        }

        $this->products = $query->orderBy('name')->get();
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->searchTerm = '';
        $this->loadProducts();
    }

    public function updatedSearchTerm()
    {
        $this->selectedCategory = null;
        $this->loadProducts();
    }

    public function addToCart($productId)
    {
        $product = Product::with('modifierGroups.modifiers')->find($productId);
        
        if (!$product) {
            return;
        }

        // Si el producto tiene modificadores, abrir modal
        if ($product->modifierGroups->isNotEmpty()) {
            $this->selectedProduct = $product;
            $this->selectedModifiers = [];
            $this->modifierQuantity = 1;
            $this->modifierNotes = '';
            $this->showModifierModal = true;
            return;
        }

        // Si no tiene modificadores, agregar directamente
        $this->addProductToCart($product);
    }
    
    public function addProductToCart($product, $modifiers = [], $quantity = 1, $notes = '')
    {
        // Buscar si ya existe en el cart (mismo producto y modificadores)
        $existingIndex = collect($this->cart)->search(function ($item) use ($product, $modifiers) {
            return $item['product_id'] == $product->id 
                && empty($item['existing_item_id'])
                && json_encode($item['modifiers']) === json_encode($modifiers);
        });

        if ($existingIndex !== false) {
            // Incrementar cantidad
            $this->cart[$existingIndex]['quantity'] += $quantity;
        } else {
            // Calcular precio total con modificadores
            $basePrice = (float) $product->price;
            $modifiersPrice = collect($modifiers)->sum('price');
            $totalPrice = $basePrice + $modifiersPrice;
            
            // Agregar nuevo item
            $this->cart[] = [
                'id' => uniqid(),
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $totalPrice,
                'quantity' => $quantity,
                'notes' => $notes,
                'modifiers' => $modifiers,
            ];
        }
    }
    
    public function toggleModifier($groupId, $modifierId, $modifierName, $modifierPrice)
    {
        $key = $groupId . '_' . $modifierId;
        
        if (isset($this->selectedModifiers[$key])) {
            unset($this->selectedModifiers[$key]);
        } else {
            $this->selectedModifiers[$key] = [
                'group_id' => $groupId,
                'modifier_id' => $modifierId,
                'name' => $modifierName,
                'price' => (float) $modifierPrice,
            ];
        }
    }
    
    public function addWithModifiers()
    {
        if (!$this->selectedProduct) {
            return;
        }
        
        $modifiers = array_values($this->selectedModifiers);
        
        $this->addProductToCart(
            $this->selectedProduct, 
            $modifiers, 
            $this->modifierQuantity,
            $this->modifierNotes
        );
        
        $this->closeModifierModal();
    }
    
    public function closeModifierModal()
    {
        $this->showModifierModal = false;
        $this->selectedProduct = null;
        $this->selectedModifiers = [];
        $this->modifierQuantity = 1;
        $this->modifierNotes = '';
    }

    public function updateQuantity($index, $quantity)
    {
        if ($quantity < 1) {
            $this->removeItem($index);
            return;
        }

        if (isset($this->cart[$index])) {
            $this->cart[$index]['quantity'] = $quantity;
        }
    }

    public function removeItem($index)
    {
        if (isset($this->cart[$index])) {
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart); // Reindexar
        }
    }

    public function updateItemNotes($index, $notes)
    {
        if (isset($this->cart[$index])) {
            $this->cart[$index]['notes'] = $notes;
        }
    }

    public function getSubtotal()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function getTaxAmount()
    {
        // Calcular impuestos (19% incluido en precio)
        $subtotal = $this->getSubtotal();
        return $subtotal * 0.19 / 1.19;
    }

    public function getTotal()
    {
        return $this->getSubtotal();
    }

    public function sendToKitchen()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'No hay items en la orden');
            return;
        }

        if ($this->guests < 1) {
            session()->flash('error', 'Debe especificar al menos 1 comensal');
            return;
        }

        try {
            DB::beginTransaction();

            if ($this->order) {
                // Orden existente - solo agregar nuevos items
                $newItems = collect($this->cart)->filter(function ($item) {
                    return !isset($item['existing_item_id']);
                });

                foreach ($newItems as $item) {
                    OrderItem::create([
                        'order_id' => $this->order->id,
                        'product_id' => $item['product_id'],
                        'name' => $item['name'],
                        'unit_price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'total' => $item['price'] * $item['quantity'],
                        'tax_amount' => ($item['price'] * $item['quantity']) * 0.19 / 1.19,
                        'notes' => $item['notes'] ?? null,
                        'status' => 'pending',
                    ]);
                }

                // Actualizar totales de la orden
                $this->order->recalculateTotals();
                $this->order->update([
                    'guests' => $this->guests,
                    'notes' => $this->notes,
                ]);

                session()->flash('success', 'Items agregados a la orden #' . $this->order->order_number);
            } else {
                // Nueva orden
                $order = Order::create([
                    'branch_id' => session('current_branch_id'),
                    'table_id' => $this->table->id,
                    'user_id' => auth()->id(),
                    'cash_session_id' => session('cash_session_id'),
                    'type' => 'dine_in',
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'guests' => $this->guests,
                    'notes' => $this->notes,
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'total' => 0,
                ]);

                // Agregar items
                foreach ($this->cart as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'name' => $item['name'],
                        'unit_price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'total' => $item['price'] * $item['quantity'],
                        'tax_amount' => ($item['price'] * $item['quantity']) * 0.19 / 1.19,
                        'notes' => $item['notes'] ?? null,
                        'status' => 'pending',
                    ]);
                }

                // Calcular totales
                $order->recalculateTotals();

                // Ocupar la mesa
                $this->table->occupy($order);

                session()->flash('success', 'Orden creada: #' . $order->order_number);
            }

            DB::commit();

            // Redirigir de vuelta a la vista de mesas
            return redirect()->route('waiter.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al procesar la orden: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.waiter.order-taking', [
            'subtotal' => $this->getSubtotal(),
            'taxAmount' => $this->getTaxAmount(),
            'total' => $this->getTotal(),
            'cartCount' => count($this->cart),
        ]);
    }
}
