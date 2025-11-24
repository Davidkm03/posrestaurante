<?php

use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\TableController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\ReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API V1
Route::prefix('v1')->name('api.v1.')->group(function () {

    // Rutas públicas (webhooks)
    Route::prefix('webhooks')->name('webhooks.')->group(function () {
        Route::post('/dian', fn() => response()->json(['status' => 'ok']))->name('dian');
        Route::post('/rappi', fn() => response()->json(['status' => 'ok']))->name('rappi');
        Route::post('/ifood', fn() => response()->json(['status' => 'ok']))->name('ifood');
        Route::post('/ubereats', fn() => response()->json(['status' => 'ok']))->name('ubereats');
    });

    // Rutas autenticadas
    Route::middleware('auth:sanctum')->group(function () {

        // Productos
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('products/search', [ProductController::class, 'search'])->name('products.search');
        Route::get('products/barcode/{barcode}', [ProductController::class, 'barcode'])->name('products.barcode');
        Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');

        // Categorías
        Route::get('categories', [ProductController::class, 'categories'])->name('categories');
        Route::get('categories/{category}/products', [ProductController::class, 'byCategory'])->name('categories.products');

        // Órdenes
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/pending', [OrderController::class, 'pending'])->name('orders.pending');
        Route::get('orders/kitchen', [OrderController::class, 'kitchen'])->name('orders.kitchen');
        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/items', [OrderController::class, 'addItems'])->name('orders.add-items');
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

        // Mesas
        Route::get('tables', [TableController::class, 'index'])->name('tables.index');
        Route::get('tables/available', [TableController::class, 'available'])->name('tables.available');
        Route::get('tables/summary', [TableController::class, 'summary'])->name('tables.summary');
        Route::get('tables/{table}', [TableController::class, 'show'])->name('tables.show');
        Route::patch('tables/{table}/status', [TableController::class, 'updateStatus'])->name('tables.update-status');

        // Clientes
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/search', [CustomerController::class, 'search'])->name('customers.search');
        Route::get('customers/document/{document}', [CustomerController::class, 'byDocument'])->name('customers.by-document');
        Route::post('customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
        Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::get('customers/{customer}/orders', [CustomerController::class, 'orders'])->name('customers.orders');

        // Reservaciones
        Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('reservations/today', [ReservationController::class, 'today'])->name('reservations.today');
        Route::get('reservations/stats', [ReservationController::class, 'stats'])->name('reservations.stats');
        Route::get('reservations/available-slots', [ReservationController::class, 'availableSlots'])->name('reservations.available-slots');
        Route::get('reservations/available-tables', [ReservationController::class, 'availableTables'])->name('reservations.available-tables');
        Route::post('reservations', [ReservationController::class, 'store'])->name('reservations.store');
        Route::get('reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
        Route::put('reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
        Route::post('reservations/{reservation}/confirm', [ReservationController::class, 'confirm'])->name('reservations.confirm');
        Route::post('reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
        Route::post('reservations/{reservation}/seat', [ReservationController::class, 'seat'])->name('reservations.seat');
        Route::post('reservations/{reservation}/no-show', [ReservationController::class, 'noShow'])->name('reservations.no-show');

    });
});
