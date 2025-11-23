<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Table;
use App\Models\Zone;
use App\Models\Category;
use App\Models\Product;
use App\Models\CashSession;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\TableStatus;
use Illuminate\Http\Request;

class POSController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');

        // Verificar si hay caja abierta
        $cashSession = CashSession::where('branch_id', $branchId)
            ->whereNull('closed_at')
            ->first();

        if (!$cashSession) {
            return redirect()->route('pos.open-cash')->with('warning', 'Debe abrir caja antes de usar el POS.');
        }

        // Cargar zonas y mesas
        $zones = Zone::where('branch_id', $branchId)
            ->where('is_active', true)
            ->with(['tables' => function ($query) {
                $query->where('is_active', true)
                    ->with(['currentOrder' => function ($q) {
                        $q->with('waiter');
                    }])
                    ->orderBy('number');
            }])
            ->orderBy('name')
            ->get();

        // Categorías para el menú
        $categories = Category::where('is_active', true)
            ->where('show_in_pos', true)
            ->orderBy('sort_order')
            ->get();

        // Órdenes pendientes
        $pendingOrders = Order::where('branch_id', $branchId)
            ->whereIn('status', [OrderStatus::PENDING->value, OrderStatus::IN_PREPARATION->value])
            ->with(['table', 'waiter'])
            ->orderBy('created_at')
            ->get();

        return view('pos.index', compact('zones', 'categories', 'pendingOrders', 'cashSession'));
    }

    public function table(Table $table)
    {
        $branchId = session('current_branch_id');

        // Verificar caja abierta
        $cashSession = CashSession::where('branch_id', $branchId)
            ->whereNull('closed_at')
            ->first();

        if (!$cashSession) {
            return redirect()->route('pos.open-cash');
        }

        // Cargar orden actual de la mesa si existe
        $currentOrder = $table->currentOrder()
            ->with(['items.product', 'items.modifiers', 'waiter', 'customer'])
            ->first();

        // Categorías y productos
        $categories = Category::where('is_active', true)
            ->where('show_in_pos', true)
            ->with(['products' => function ($query) {
                $query->where('is_active', true)
                    ->where('show_in_pos', true)
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        return view('pos.table', compact('table', 'currentOrder', 'categories', 'cashSession'));
    }

    public function quickSale()
    {
        $branchId = session('current_branch_id');

        $cashSession = CashSession::where('branch_id', $branchId)
            ->whereNull('closed_at')
            ->first();

        if (!$cashSession) {
            return redirect()->route('pos.open-cash');
        }

        $categories = Category::where('is_active', true)
            ->where('show_in_pos', true)
            ->with(['products' => function ($query) {
                $query->where('is_active', true)
                    ->where('show_in_pos', true)
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        return view('pos.quick-sale', compact('categories', 'cashSession'));
    }

    public function takeaway()
    {
        $branchId = session('current_branch_id');

        $cashSession = CashSession::where('branch_id', $branchId)
            ->whereNull('closed_at')
            ->first();

        if (!$cashSession) {
            return redirect()->route('pos.open-cash');
        }

        $categories = Category::where('is_active', true)
            ->where('show_in_pos', true)
            ->with(['products' => function ($query) {
                $query->where('is_active', true)
                    ->where('show_in_pos', true)
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        // Órdenes para llevar pendientes
        $pendingOrders = Order::where('branch_id', $branchId)
            ->where('type', OrderType::TAKEAWAY->value)
            ->whereIn('status', [OrderStatus::PENDING->value, OrderStatus::IN_PREPARATION->value, OrderStatus::READY->value])
            ->with(['customer', 'items'])
            ->orderBy('created_at')
            ->get();

        return view('pos.takeaway', compact('categories', 'pendingOrders', 'cashSession'));
    }

    public function delivery()
    {
        $branchId = session('current_branch_id');

        $cashSession = CashSession::where('branch_id', $branchId)
            ->whereNull('closed_at')
            ->first();

        if (!$cashSession) {
            return redirect()->route('pos.open-cash');
        }

        $categories = Category::where('is_active', true)
            ->where('show_in_pos', true)
            ->with(['products' => function ($query) {
                $query->where('is_active', true)
                    ->where('show_in_pos', true)
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        // Órdenes de domicilio pendientes
        $pendingOrders = Order::where('branch_id', $branchId)
            ->where('type', OrderType::DELIVERY->value)
            ->whereIn('status', [
                OrderStatus::PENDING->value,
                OrderStatus::IN_PREPARATION->value,
                OrderStatus::READY->value,
                OrderStatus::ON_DELIVERY->value
            ])
            ->with(['customer', 'items', 'deliveryDriver'])
            ->orderBy('created_at')
            ->get();

        return view('pos.delivery', compact('categories', 'pendingOrders', 'cashSession'));
    }

    public function searchProduct(Request $request)
    {
        $search = $request->get('q', '');

        $products = Product::where('is_active', true)
            ->where('show_in_pos', true)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', $search);
            })
            ->with('category')
            ->limit(20)
            ->get();

        return response()->json($products);
    }

    public function getProduct(Product $product)
    {
        $product->load(['modifierGroups.modifiers', 'category']);

        return response()->json($product);
    }

    public function openCash()
    {
        $branchId = session('current_branch_id');

        // Verificar si ya hay caja abierta
        $existingSession = CashSession::where('branch_id', $branchId)
            ->whereNull('closed_at')
            ->first();

        if ($existingSession) {
            return redirect()->route('pos.index');
        }

        $cashRegisters = \App\Models\CashRegister::where('branch_id', $branchId)
            ->where('is_active', true)
            ->get();

        return view('pos.open-cash', compact('cashRegisters'));
    }

    public function storeOpenCash(Request $request)
    {
        $branchId = session('current_branch_id');

        $request->validate([
            'cash_register_id' => 'required|exists:cash_registers,id',
            'opening_amount' => 'required|numeric|min:0',
        ]);

        $session = CashSession::create([
            'branch_id' => $branchId,
            'cash_register_id' => $request->cash_register_id,
            'opened_by' => auth()->id(),
            'opened_at' => now(),
            'opening_amount' => $request->opening_amount,
        ]);

        session(['cash_session_id' => $session->id]);

        return redirect()->route('pos.index')->with('success', 'Caja abierta exitosamente.');
    }
}
