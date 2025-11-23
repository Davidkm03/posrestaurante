<?php

use App\Http\Controllers\Api\V1\OrderApiController;
use App\Http\Controllers\Api\V1\ProductApiController;
use App\Http\Controllers\Api\V1\ReportApiController;
use App\Http\Controllers\Api\V1\WebhookController;
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
        Route::post('/dian', [WebhookController::class, 'dian'])->name('dian');
        Route::post('/rappi', [WebhookController::class, 'rappi'])->name('rappi');
        Route::post('/ifood', [WebhookController::class, 'ifood'])->name('ifood');
        Route::post('/ubereats', [WebhookController::class, 'ubereats'])->name('ubereats');
        Route::post('/payment-gateway', [WebhookController::class, 'paymentGateway'])->name('payment-gateway');
    });

    // Rutas autenticadas
    Route::middleware('auth:sanctum')->group(function () {

        // Productos
        Route::apiResource('products', ProductApiController::class)->only(['index', 'show']);
        Route::get('products/search', [ProductApiController::class, 'search'])->name('products.search');
        Route::get('categories', [ProductApiController::class, 'categories'])->name('categories');
        Route::get('categories/{category}/products', [ProductApiController::class, 'categoryProducts'])->name('categories.products');

        // Órdenes
        Route::apiResource('orders', OrderApiController::class);
        Route::post('orders/{order}/items', [OrderApiController::class, 'addItem'])->name('orders.add-item');
        Route::delete('orders/{order}/items/{item}', [OrderApiController::class, 'removeItem'])->name('orders.remove-item');
        Route::post('orders/{order}/pay', [OrderApiController::class, 'pay'])->name('orders.pay');
        Route::post('orders/{order}/cancel', [OrderApiController::class, 'cancel'])->name('orders.cancel');

        // Reportes
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/sales/summary', [ReportApiController::class, 'salesSummary'])->name('sales.summary');
            Route::get('/sales/by-product', [ReportApiController::class, 'salesByProduct'])->name('sales.by-product');
            Route::get('/sales/by-category', [ReportApiController::class, 'salesByCategory'])->name('sales.by-category');
            Route::get('/sales/by-hour', [ReportApiController::class, 'salesByHour'])->name('sales.by-hour');
            Route::get('/inventory/stock', [ReportApiController::class, 'inventoryStock'])->name('inventory.stock');
        });

        // Mesas
        Route::get('tables', [OrderApiController::class, 'tables'])->name('tables');
        Route::get('tables/{table}', [OrderApiController::class, 'table'])->name('tables.show');
        Route::get('tables/{table}/orders', [OrderApiController::class, 'tableOrders'])->name('tables.orders');

        // Clientes
        Route::get('customers/search', [OrderApiController::class, 'searchCustomers'])->name('customers.search');
        Route::post('customers', [OrderApiController::class, 'createCustomer'])->name('customers.store');

    });
});
