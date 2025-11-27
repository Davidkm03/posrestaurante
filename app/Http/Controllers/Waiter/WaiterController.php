<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Table;
use App\Models\Zone;
use Illuminate\Http\Request;

class WaiterController extends Controller
{
    public function index()
    {
        $branchId = session('current_branch_id');
        $userId = auth()->id();

        // Get zones with tables for current branch
        $zones = Zone::where('branch_id', $branchId)
            ->with(['tables' => function ($query) {
                $query->with('currentOrder')
                    ->orderBy('number');
            }])
            ->orderBy('name')
            ->get();

        // Get active orders for this waiter
        $myOrders = Order::where('user_id', $userId)
            ->where('branch_id', $branchId)
            ->whereIn('status', ['pending', 'in_preparation', 'ready'])
            ->with(['table.zone', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('waiter.index', compact('zones', 'myOrders'));
    }

    public function myOrders()
    {
        $branchId = session('current_branch_id');
        $userId = auth()->id();

        $orders = Order::where('user_id', $userId)
            ->where('branch_id', $branchId)
            ->whereIn('status', ['pending', 'in_preparation', 'ready', 'delivered'])
            ->with(['table.zone', 'items.product', 'customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('waiter.orders', compact('orders'));
    }
}
