<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Zone;
use App\Enums\TableStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $branchId = $request->user()->currentBranchId();

        $zones = Zone::where('branch_id', $branchId)
            ->where('is_active', true)
            ->with(['tables' => function ($query) {
                $query->where('is_active', true)
                    ->with(['currentOrder' => fn($q) => $q->with('waiter')])
                    ->orderBy('number');
            }])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $zones,
        ]);
    }

    public function show(Table $table): JsonResponse
    {
        $table->load([
            'zone',
            'currentOrder.items.product',
            'currentOrder.items.modifiers',
            'currentOrder.waiter',
            'currentOrder.customer',
        ]);

        return response()->json([
            'success' => true,
            'data' => $table,
        ]);
    }

    public function updateStatus(Request $request, Table $table): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_column(TableStatus::cases(), 'value')),
        ]);

        $table->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Estado de mesa actualizado',
            'data' => $table->fresh(),
        ]);
    }

    public function available(Request $request): JsonResponse
    {
        $branchId = $request->user()->currentBranchId();

        $tables = Table::where('branch_id', $branchId)
            ->where('is_active', true)
            ->where('status', TableStatus::FREE->value)
            ->with('zone')
            ->orderBy('number')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tables,
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        $branchId = $request->user()->currentBranchId();

        $tables = Table::where('branch_id', $branchId)
            ->where('is_active', true)
            ->get();

        $summary = [
            'total' => $tables->count(),
            'free' => $tables->where('status', TableStatus::FREE->value)->count(),
            'occupied' => $tables->where('status', TableStatus::OCCUPIED->value)->count(),
            'reserved' => $tables->where('status', TableStatus::RESERVED->value)->count(),
            'cleaning' => $tables->where('status', TableStatus::CLEANING->value)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }
}
