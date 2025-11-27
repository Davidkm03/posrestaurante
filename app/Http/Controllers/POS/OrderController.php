<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use App\Models\Product;
use App\Models\CashSession;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\TableStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $branchId = session('current_branch_id');
        $cashSessionId = session('cash_session_id');

        if (!$cashSessionId) {
            return response()->json(['error' => 'No hay caja abierta'], 400);
        }

        $validated = $request->validate([
            'type' => 'required|in:' . implode(',', array_column(OrderType::cases(), 'value')),
            'table_id' => 'nullable|exists:tables,id',
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string|max:255',
            'items.*.modifiers' => 'nullable|array',
            'notes' => 'nullable|string|max:500',
            'guests' => 'nullable|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Generar número de orden
            $orderNumber = $this->generateOrderNumber($branchId);

            // Crear orden
            $order = Order::create([
                'branch_id' => $branchId,
                'cash_session_id' => $cashSessionId,
                'order_number' => $orderNumber,
                'type' => $validated['type'],
                'table_id' => $validated['table_id'] ?? null,
                'customer_id' => $validated['customer_id'] ?? null,
                'user_id' => auth()->id(),
                'status' => OrderStatus::PENDING->value,
                'guests' => $validated['guests'] ?? 1,
                'notes' => $validated['notes'] ?? null,
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total' => 0,
            ]);

            // Agregar items
            $subtotal = 0;
            $totalTax = 0;

            foreach ($validated['items'] as $itemData) {
                $product = Product::find($itemData['product_id']);

                // Calcular precio con modificadores
                $unitPrice = $product->price;
                $modifiersTotal = 0;

                if (!empty($itemData['modifiers'])) {
                    foreach ($itemData['modifiers'] as $modifierId) {
                        $modifier = \App\Models\Modifier::find($modifierId);
                        if ($modifier) {
                            $modifiersTotal += $modifier->price;
                        }
                    }
                }

                $unitPrice += $modifiersTotal;
                $itemSubtotal = $unitPrice * $itemData['quantity'];

                // Calcular impuesto
                $taxAmount = 0;
                if ($product->tax_included) {
                    // Extraer impuesto del precio
                    $taxAmount = $itemSubtotal - ($itemSubtotal / (1 + $product->tax_percentage / 100));
                } else {
                    // Agregar impuesto al precio
                    $taxAmount = $itemSubtotal * ($product->tax_percentage / 100);
                }

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal' => $itemSubtotal,
                    'tax_type' => $product->tax_type,
                    'tax_percentage' => $product->tax_percentage,
                    'tax_amount' => $taxAmount,
                    'notes' => $itemData['notes'] ?? null,
                    'status' => 'pending',
                ]);

                // Guardar modificadores
                if (!empty($itemData['modifiers'])) {
                    $orderItem->modifiers()->attach($itemData['modifiers']);
                }

                $subtotal += $itemSubtotal;
                $totalTax += $taxAmount;
            }

            // Actualizar totales de la orden
            $order->update([
                'subtotal' => $subtotal,
                'tax' => $totalTax,
                'total' => $subtotal,
            ]);

            // Actualizar estado de la mesa si aplica
            if ($order->table_id) {
                Table::where('id', $order->table_id)->update([
                    'status' => TableStatus::OCCUPIED->value,
                    'current_order_id' => $order->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'order' => $order->load(['items.product', 'items.modifiers', 'table']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al crear la orden: ' . $e->getMessage()], 500);
        }
    }

    public function addItems(Request $request, Order $order)
    {
        if ($order->status === OrderStatus::PAID->value || $order->status === OrderStatus::CANCELLED->value) {
            return response()->json(['error' => 'No se pueden agregar items a esta orden'], 400);
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string|max:255',
            'items.*.modifiers' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['items'] as $itemData) {
                $product = Product::find($itemData['product_id']);

                $unitPrice = $product->price;
                $modifiersTotal = 0;

                if (!empty($itemData['modifiers'])) {
                    foreach ($itemData['modifiers'] as $modifierId) {
                        $modifier = \App\Models\Modifier::find($modifierId);
                        if ($modifier) {
                            $modifiersTotal += $modifier->price;
                        }
                    }
                }

                $unitPrice += $modifiersTotal;
                $itemSubtotal = $unitPrice * $itemData['quantity'];

                $taxAmount = 0;
                if ($product->tax_included) {
                    $taxAmount = $itemSubtotal - ($itemSubtotal / (1 + $product->tax_percentage / 100));
                } else {
                    $taxAmount = $itemSubtotal * ($product->tax_percentage / 100);
                }

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal' => $itemSubtotal,
                    'tax_type' => $product->tax_type,
                    'tax_percentage' => $product->tax_percentage,
                    'tax_amount' => $taxAmount,
                    'notes' => $itemData['notes'] ?? null,
                    'status' => 'pending',
                ]);

                if (!empty($itemData['modifiers'])) {
                    $orderItem->modifiers()->attach($itemData['modifiers']);
                }
            }

            // Recalcular totales
            $this->recalculateOrder($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'order' => $order->fresh(['items.product', 'items.modifiers']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al agregar items: ' . $e->getMessage()], 500);
        }
    }

    public function removeItem(Order $order, OrderItem $item)
    {
        if ($order->status === OrderStatus::PAID->value) {
            return response()->json(['error' => 'No se pueden eliminar items de una orden pagada'], 400);
        }

        try {
            DB::beginTransaction();

            $item->delete();

            // Si no quedan items, cancelar la orden
            if ($order->items()->count() === 0) {
                $order->update(['status' => OrderStatus::CANCELLED->value]);

                if ($order->table_id) {
                    Table::where('id', $order->table_id)->update([
                        'status' => TableStatus::FREE->value,
                        'current_order_id' => null,
                    ]);
                }
            } else {
                $this->recalculateOrder($order);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'order' => $order->fresh(['items.product', 'items.modifiers']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al eliminar item: ' . $e->getMessage()], 500);
        }
    }

    public function updateItemQuantity(Request $request, Order $order, OrderItem $item)
    {
        if ($order->status === OrderStatus::PAID->value) {
            return response()->json(['error' => 'No se puede modificar una orden pagada'], 400);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $item->update([
                'quantity' => $validated['quantity'],
                'subtotal' => $item->unit_price * $validated['quantity'],
                'tax_amount' => ($item->unit_price * $validated['quantity']) * ($item->tax_percentage / 100),
            ]);

            $this->recalculateOrder($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'order' => $order->fresh(['items.product', 'items.modifiers']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al actualizar cantidad: ' . $e->getMessage()], 500);
        }
    }

    public function applyDiscount(Request $request, Order $order)
    {
        $validated = $request->validate([
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'discount_reason' => 'nullable|string|max:255',
        ]);

        $discount = 0;
        if ($validated['discount_type'] === 'percentage') {
            $discount = $order->subtotal * ($validated['discount_value'] / 100);
        } else {
            $discount = min($validated['discount_value'], $order->subtotal);
        }

        $order->update([
            'discount' => $discount,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'discount_reason' => $validated['discount_reason'] ?? null,
            'discount_by' => auth()->id(),
            'total' => $order->subtotal - $discount,
        ]);

        return response()->json([
            'success' => true,
            'order' => $order->fresh(['items.product', 'items.modifiers']),
        ]);
    }

    public function cancel(Request $request, Order $order)
    {
        if (!$order->canBeCancelled()) {
            return response()->json(['error' => 'Esta orden no puede ser cancelada'], 400);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $order->update([
                'status' => OrderStatus::CANCELLED->value,
                'cancellation_reason' => $validated['reason'],
                'cancelled_at' => now(),
                'cancelled_by' => auth()->id(),
            ]);

            if ($order->table_id) {
                Table::where('id', $order->table_id)->update([
                    'status' => TableStatus::FREE->value,
                    'current_order_id' => null,
                ]);
            }

            DB::commit();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al cancelar: ' . $e->getMessage()], 500);
        }
    }

    public function sendToKitchen(Order $order)
    {
        $order->update([
            'status' => OrderStatus::IN_PREPARATION->value,
            'sent_to_kitchen_at' => now(),
        ]);

        // Actualizar items a preparación
        $order->items()
            ->where('status', 'pending')
            ->update(['status' => 'preparing']);

        // Aquí se podría disparar evento para notificar a cocina
        // event(new OrderSentToKitchen($order));

        return response()->json([
            'success' => true,
            'order' => $order->fresh(['items.product']),
        ]);
    }

    protected function generateOrderNumber(int $branchId): string
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $lastOrder = Order::where('branch_id', $branchId)
            ->whereDate('created_at', today())
            ->orderByDesc('id')
            ->first();

        $sequence = $lastOrder ? intval(substr($lastOrder->order_number, -4)) + 1 : 1;

        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    protected function recalculateOrder(Order $order): void
    {
        $items = $order->items()->get();

        $subtotal = $items->sum('subtotal');
        $tax = $items->sum('tax_amount');

        $discount = $order->discount ?? 0;
        $total = $subtotal - $discount;

        $order->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);
    }
}
