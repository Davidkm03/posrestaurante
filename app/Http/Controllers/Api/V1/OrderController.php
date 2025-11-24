<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $branchId = $request->user()->currentBranchId();

        $query = Order::where('branch_id', $branchId)
            ->with(['table', 'customer', 'items.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->latest()->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->create([
                'branch_id' => $request->user()->currentBranchId(),
                'cash_session_id' => session('cash_session_id'),
                'type' => $request->type,
                'table_id' => $request->table_id,
                'customer_id' => $request->customer_id,
                'waiter_id' => $request->user()->id,
                'items' => $request->items,
                'notes' => $request->notes,
                'guests' => $request->guests,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Orden creada exitosamente',
                'data' => $order,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function show(Order $order): JsonResponse
    {
        $order->load([
            'table.zone',
            'customer',
            'waiter',
            'items.product',
            'items.modifiers',
            'payments.method',
        ]);

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    public function addItems(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string|max:255',
            'items.*.modifiers' => 'nullable|array',
        ]);

        try {
            $order = $this->orderService->addItems($order, $request->items);

            return response()->json([
                'success' => true,
                'message' => 'Items agregados',
                'data' => $order,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_column(OrderStatus::cases(), 'value')),
        ]);

        try {
            $method = match ($request->status) {
                OrderStatus::IN_PREPARATION->value => 'sendToKitchen',
                OrderStatus::READY->value => 'markReady',
                OrderStatus::DELIVERED->value => 'markDelivered',
                default => null,
            };

            if ($method) {
                $order = $this->orderService->$method($order);
            } else {
                $order->update(['status' => $request->status]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado',
                'data' => $order->fresh(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if (!$order->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta orden no puede ser cancelada',
            ], 400);
        }

        $order = $this->orderService->cancel($order, $request->reason);

        return response()->json([
            'success' => true,
            'message' => 'Orden cancelada',
            'data' => $order,
        ]);
    }

    public function pending(Request $request): JsonResponse
    {
        $branchId = $request->user()->currentBranchId();

        $orders = Order::where('branch_id', $branchId)
            ->whereIn('status', [
                OrderStatus::PENDING->value,
                OrderStatus::IN_PREPARATION->value,
            ])
            ->with(['table', 'items.product', 'waiter'])
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function kitchen(Request $request): JsonResponse
    {
        $branchId = $request->user()->currentBranchId();

        $orders = Order::where('branch_id', $branchId)
            ->whereIn('status', [
                OrderStatus::PENDING->value,
                OrderStatus::IN_PREPARATION->value,
                OrderStatus::READY->value,
            ])
            ->with([
                'table',
                'items' => fn($q) => $q->whereIn('status', ['pending', 'preparing']),
                'items.product.category',
                'items.modifiers',
            ])
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders,
            'stats' => [
                'pending' => $orders->where('status', OrderStatus::PENDING->value)->count(),
                'preparing' => $orders->where('status', OrderStatus::IN_PREPARATION->value)->count(),
                'ready' => $orders->where('status', OrderStatus::READY->value)->count(),
            ],
        ]);
    }
}
