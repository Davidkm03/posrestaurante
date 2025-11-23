<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function display(Request $request)
    {
        $branchId = session('current_branch_id');

        $orders = Order::where('branch_id', $branchId)
            ->whereIn('status', [
                OrderStatus::PENDING->value,
                OrderStatus::IN_PREPARATION->value,
            ])
            ->with([
                'table',
                'items' => function ($query) {
                    $query->whereIn('status', ['pending', 'preparing'])
                        ->with(['product.category', 'modifiers']);
                },
                'waiter',
            ])
            ->orderBy('created_at')
            ->get();

        // Agrupar por estado
        $pendingOrders = $orders->where('status', OrderStatus::PENDING->value);
        $preparingOrders = $orders->where('status', OrderStatus::IN_PREPARATION->value);

        // Órdenes listas para servir
        $readyOrders = Order::where('branch_id', $branchId)
            ->where('status', OrderStatus::READY->value)
            ->with(['table', 'waiter'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Estadísticas
        $stats = [
            'pending_count' => $pendingOrders->count(),
            'preparing_count' => $preparingOrders->count(),
            'ready_count' => $readyOrders->count(),
            'avg_prep_time' => $this->calculateAveragePrepTime($branchId),
        ];

        return view('kitchen.display', compact('pendingOrders', 'preparingOrders', 'readyOrders', 'stats'));
    }

    public function startPreparation(Order $order)
    {
        if ($order->status !== OrderStatus::PENDING->value) {
            return response()->json(['error' => 'La orden ya está en preparación'], 400);
        }

        $order->update([
            'status' => OrderStatus::IN_PREPARATION->value,
            'preparation_started_at' => now(),
        ]);

        $order->items()->update(['status' => 'preparing']);

        return response()->json([
            'success' => true,
            'order' => $order->fresh(['items.product']),
        ]);
    }

    public function markItemReady(OrderItem $item)
    {
        $item->update([
            'status' => 'ready',
            'prepared_at' => now(),
        ]);

        // Verificar si todos los items están listos
        $order = $item->order;
        $pendingItems = $order->items()->whereIn('status', ['pending', 'preparing'])->count();

        if ($pendingItems === 0) {
            $order->update([
                'status' => OrderStatus::READY->value,
                'ready_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'all_ready' => $pendingItems === 0,
        ]);
    }

    public function markOrderReady(Order $order)
    {
        $order->update([
            'status' => OrderStatus::READY->value,
            'ready_at' => now(),
        ]);

        $order->items()->update([
            'status' => 'ready',
            'prepared_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'order' => $order->fresh(),
        ]);
    }

    public function recallOrder(Order $order)
    {
        // Volver a cocina una orden que estaba lista
        if ($order->status !== OrderStatus::READY->value) {
            return response()->json(['error' => 'Solo se pueden recuperar órdenes listas'], 400);
        }

        $order->update([
            'status' => OrderStatus::IN_PREPARATION->value,
            'ready_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'order' => $order->fresh(),
        ]);
    }

    public function bumpOrder(Order $order)
    {
        // Marcar como entregada y sacar de pantalla
        $order->update([
            'status' => OrderStatus::DELIVERED->value,
            'delivered_at' => now(),
        ]);

        $order->items()->update(['status' => 'delivered']);

        return response()->json(['success' => true]);
    }

    public function getOrders(Request $request)
    {
        $branchId = session('current_branch_id');

        $orders = Order::where('branch_id', $branchId)
            ->whereIn('status', [
                OrderStatus::PENDING->value,
                OrderStatus::IN_PREPARATION->value,
                OrderStatus::READY->value,
            ])
            ->with([
                'table',
                'items.product.category',
                'items.modifiers',
                'waiter',
            ])
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'orders' => $orders,
            'stats' => [
                'pending' => $orders->where('status', OrderStatus::PENDING->value)->count(),
                'preparing' => $orders->where('status', OrderStatus::IN_PREPARATION->value)->count(),
                'ready' => $orders->where('status', OrderStatus::READY->value)->count(),
            ],
        ]);
    }

    protected function calculateAveragePrepTime(int $branchId): int
    {
        $avgMinutes = Order::where('branch_id', $branchId)
            ->whereNotNull('preparation_started_at')
            ->whereNotNull('ready_at')
            ->whereDate('created_at', today())
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, preparation_started_at, ready_at)) as avg_time')
            ->value('avg_time');

        return (int) ($avgMinutes ?? 0);
    }
}
