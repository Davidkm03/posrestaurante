<?php

use App\Http\Controllers\Waiter\WaiterController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'check.role:mesero'])->prefix('waiter')->name('waiter.')->group(function () {
    Route::get('/', [WaiterController::class, 'index'])->name('index');
    Route::get('/orders', [WaiterController::class, 'myOrders'])->name('orders');
    
    // Order taking for a specific table
    Route::get('/table/{table}/order', function (\App\Models\Table $table) {
        return view('waiter.order-taking', compact('table'));
    })->name('table.order');
    
    // Mark order as delivered
    Route::patch('/orders/{order}/deliver', function (\App\Models\Order $order) {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        
        $order->update(['status' => 'delivered']);
        
        return redirect()->route('waiter.orders')->with('success', 'Orden marcada como servida');
    })->name('orders.deliver');
});
