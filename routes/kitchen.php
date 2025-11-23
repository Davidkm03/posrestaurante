<?php

use App\Http\Controllers\Kitchen\BarDisplayController;
use App\Http\Controllers\Kitchen\KitchenDisplayController;
use App\Http\Controllers\Kitchen\PrepStationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Kitchen Display System Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'permission:kitchen.access'])->prefix('kitchen')->name('kitchen.')->group(function () {

    // Pantalla principal de cocina (KDS)
    Route::get('/', [KitchenDisplayController::class, 'index'])->name('index');

    // API para actualizar pedidos
    Route::get('/orders', [KitchenDisplayController::class, 'orders'])->name('orders');
    Route::get('/orders/pending', [KitchenDisplayController::class, 'pendingOrders'])->name('orders.pending');
    Route::get('/orders/{order}', [KitchenDisplayController::class, 'show'])->name('orders.show');

    // Actualizar estado de items
    Route::post('/items/{item}/start', [KitchenDisplayController::class, 'startItem'])->name('items.start');
    Route::post('/items/{item}/ready', [KitchenDisplayController::class, 'readyItem'])->name('items.ready');
    Route::post('/items/{item}/bump', [KitchenDisplayController::class, 'bumpItem'])->name('items.bump');

    // Actualizar estado de orden completa
    Route::post('/orders/{order}/start-all', [KitchenDisplayController::class, 'startAll'])->name('orders.start-all');
    Route::post('/orders/{order}/ready-all', [KitchenDisplayController::class, 'readyAll'])->name('orders.ready-all');
    Route::post('/orders/{order}/bump', [KitchenDisplayController::class, 'bumpOrder'])->name('orders.bump');

    // Recall (ver órdenes completadas)
    Route::get('/recall', [KitchenDisplayController::class, 'recall'])->name('recall');
    Route::post('/orders/{order}/recall', [KitchenDisplayController::class, 'recallOrder'])->name('orders.recall');

    // Vista All Day (totales del día)
    Route::get('/all-day', [KitchenDisplayController::class, 'allDay'])->name('all-day');

    // Estadísticas
    Route::get('/stats', [KitchenDisplayController::class, 'stats'])->name('stats');

    // Pantalla de Bar
    Route::prefix('bar')->name('bar.')->group(function () {
        Route::get('/', [BarDisplayController::class, 'index'])->name('index');
        Route::get('/orders', [BarDisplayController::class, 'orders'])->name('orders');
    });

    // Estaciones de preparación específicas
    Route::prefix('stations')->name('stations.')->group(function () {
        Route::get('/', [PrepStationController::class, 'index'])->name('index');
        Route::get('/{station}', [PrepStationController::class, 'show'])->name('show');
        Route::get('/{station}/orders', [PrepStationController::class, 'orders'])->name('orders');
    });

    // Configuración de pantalla
    Route::get('/settings', [KitchenDisplayController::class, 'settings'])->name('settings');
    Route::post('/settings', [KitchenDisplayController::class, 'updateSettings'])->name('settings.update');
});
