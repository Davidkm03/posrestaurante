<?php

use App\Http\Controllers\POS\DeliveryController;
use App\Http\Controllers\POS\OrderController;
use App\Http\Controllers\POS\PaymentController;
use App\Http\Controllers\POS\POSController;
use App\Http\Controllers\POS\QuickSaleController;
use App\Http\Controllers\POS\TableMapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| POS Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'permission:pos.access'])->prefix('pos')->name('pos.')->group(function () {

    // Pantalla principal POS
    Route::get('/', [POSController::class, 'index'])->name('index');

    // Productos y categorías
    Route::get('categories', [POSController::class, 'categories'])->name('categories');
    Route::get('products', [POSController::class, 'products'])->name('products');
    Route::get('products/search', [POSController::class, 'searchProducts'])->name('products.search');
    Route::get('products/{product}', [POSController::class, 'product'])->name('products.show');
    Route::get('products/{product}/modifiers', [POSController::class, 'productModifiers'])->name('products.modifiers');

    // Órdenes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::put('/{order}', [OrderController::class, 'update'])->name('update');
        Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');

        // Items
        Route::post('/{order}/items', [OrderController::class, 'addItem'])->name('items.add');
        Route::put('/{order}/items/{item}', [OrderController::class, 'updateItem'])->name('items.update');
        Route::delete('/{order}/items/{item}', [OrderController::class, 'removeItem'])->name('items.remove');

        // Acciones
        Route::post('/{order}/send-to-kitchen', [OrderController::class, 'sendToKitchen'])->name('send-to-kitchen');
        Route::post('/{order}/apply-discount', [OrderController::class, 'applyDiscount'])->name('apply-discount');
        Route::post('/{order}/remove-discount', [OrderController::class, 'removeDiscount'])->name('remove-discount');
        Route::post('/{order}/add-tip', [OrderController::class, 'addTip'])->name('add-tip');
        Route::post('/{order}/transfer-table', [OrderController::class, 'transferTable'])->name('transfer-table');
        Route::post('/{order}/split', [OrderController::class, 'split'])->name('split');
        Route::post('/{order}/merge', [OrderController::class, 'merge'])->name('merge');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::get('/{order}/pre-bill', [OrderController::class, 'preBill'])->name('pre-bill');
        Route::post('/{order}/reprint-kitchen', [OrderController::class, 'reprintKitchen'])->name('reprint-kitchen');
    });

    // Pagos
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::post('/process', [PaymentController::class, 'process'])->name('process');
        Route::post('/process-multiple', [PaymentController::class, 'processMultiple'])->name('process-multiple');
        Route::get('/methods', [PaymentController::class, 'methods'])->name('methods');
        Route::post('/refund', [PaymentController::class, 'refund'])->name('refund');
    });

    // Mesas
    Route::prefix('tables')->name('tables.')->group(function () {
        Route::get('/', [TableMapController::class, 'index'])->name('index');
        Route::get('/map', [TableMapController::class, 'map'])->name('map');
        Route::get('/{table}', [TableMapController::class, 'show'])->name('show');
        Route::post('/{table}/open', [TableMapController::class, 'open'])->name('open');
        Route::post('/{table}/close', [TableMapController::class, 'close'])->name('close');
        Route::post('/{table}/transfer', [TableMapController::class, 'transfer'])->name('transfer');
        Route::post('/merge', [TableMapController::class, 'merge'])->name('merge');
    });

    // Clientes
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/search', [POSController::class, 'searchCustomers'])->name('search');
        Route::post('/', [POSController::class, 'storeCustomer'])->name('store');
        Route::get('/{customer}', [POSController::class, 'customer'])->name('show');
    });

    // Caja
    Route::prefix('cash')->name('cash.')->group(function () {
        Route::get('/status', [POSController::class, 'cashStatus'])->name('status');
        Route::post('/open', [POSController::class, 'openCash'])->name('open');
        Route::post('/close', [POSController::class, 'closeCash'])->name('close');
        Route::post('/movement', [POSController::class, 'cashMovement'])->name('movement');
        Route::get('/x-report', [POSController::class, 'xReport'])->name('x-report');
    });

    // Venta rápida (sin mesa)
    Route::get('/quick-sale', [QuickSaleController::class, 'index'])->name('quick-sale');

    // Domicilios
    Route::prefix('delivery')->name('delivery.')->group(function () {
        Route::get('/', [DeliveryController::class, 'index'])->name('index');
        Route::post('/', [DeliveryController::class, 'store'])->name('store');
        Route::get('/zones', [DeliveryController::class, 'zones'])->name('zones');
        Route::get('/calculate-fee', [DeliveryController::class, 'calculateFee'])->name('calculate-fee');
    });

    // Impresión
    Route::prefix('print')->name('print.')->group(function () {
        Route::post('/receipt/{order}', [POSController::class, 'printReceipt'])->name('receipt');
        Route::post('/kitchen/{order}', [POSController::class, 'printKitchen'])->name('kitchen');
        Route::post('/pre-bill/{order}', [POSController::class, 'printPreBill'])->name('pre-bill');
    });
});
