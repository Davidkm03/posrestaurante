<?php

use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CashRegisterController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ComboController;
use App\Http\Controllers\Admin\CreditNoteController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DebitNoteController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ModifierController;
use App\Http\Controllers\Admin\PrinterController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\ResolutionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ZoneController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Productos y Menú
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/toggle-active', [ProductController::class, 'toggleActive'])->name('products.toggle-active');
    Route::post('products/{product}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');

    Route::resource('categories', CategoryController::class);
    Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

    Route::resource('modifiers', ModifierController::class);
    Route::resource('combos', ComboController::class);

    // Mesas y Zonas
    Route::resource('zones', ZoneController::class);
    Route::resource('tables', TableController::class);
    Route::get('tables-map', [TableController::class, 'map'])->name('tables.map');
    Route::post('tables/{table}/update-position', [TableController::class, 'updatePosition'])->name('tables.update-position');

    // Reservaciones
    Route::resource('reservations', ReservationController::class);
    Route::get('reservations-calendar', [ReservationController::class, 'calendar'])->name('reservations.calendar');
    Route::post('reservations/{reservation}/confirm', [ReservationController::class, 'confirm'])->name('reservations.confirm');
    Route::post('reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');

    // Clientes
    Route::resource('customers', CustomerController::class);
    Route::get('customers/{customer}/orders', [CustomerController::class, 'orders'])->name('customers.orders');
    Route::get('customers/{customer}/invoices', [CustomerController::class, 'invoices'])->name('customers.invoices');
    Route::post('customers/{customer}/add-points', [CustomerController::class, 'addPoints'])->name('customers.add-points');

    // Facturación
    Route::resource('invoices', InvoiceController::class)->only(['index', 'show']);
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::get('invoices/{invoice}/xml', [InvoiceController::class, 'xml'])->name('invoices.xml');
    Route::post('invoices/{invoice}/resend', [InvoiceController::class, 'resend'])->name('invoices.resend');
    Route::post('invoices/{invoice}/send-email', [InvoiceController::class, 'sendEmail'])->name('invoices.send-email');

    Route::resource('credit-notes', CreditNoteController::class);
    Route::resource('debit-notes', DebitNoteController::class);

    // Caja
    Route::resource('cash-registers', CashRegisterController::class);
    Route::get('cash-sessions', [CashRegisterController::class, 'sessions'])->name('cash.sessions');
    Route::get('cash-sessions/{session}', [CashRegisterController::class, 'showSession'])->name('cash.sessions.show');
    Route::get('cash-sessions/{session}/report', [CashRegisterController::class, 'sessionReport'])->name('cash.sessions.report');

    // Inventario
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::resource('ingredients', \App\Http\Controllers\Admin\IngredientController::class);
        Route::resource('recipes', \App\Http\Controllers\Admin\RecipeController::class);
        Route::get('movements', [InventoryController::class, 'movements'])->name('movements');
        Route::post('movements', [InventoryController::class, 'storeMovement'])->name('movements.store');
        Route::get('stock-take', [InventoryController::class, 'stockTake'])->name('stock-take');
        Route::post('stock-take', [InventoryController::class, 'saveStockTake'])->name('stock-take.save');
        Route::get('alerts', [InventoryController::class, 'alerts'])->name('alerts');
    });

    // Proveedores y Compras
    Route::resource('suppliers', SupplierController::class);
    Route::resource('purchases', PurchaseController::class);
    Route::post('purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');

    // Promociones
    Route::resource('promotions', PromotionController::class);
    Route::post('promotions/{promotion}/toggle-active', [PromotionController::class, 'toggleActive'])->name('promotions.toggle-active');

    // Reportes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('products', [ReportController::class, 'products'])->name('products');
        Route::get('inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('taxes', [ReportController::class, 'taxes'])->name('taxes');
        Route::get('employees', [ReportController::class, 'employees'])->name('employees');
        Route::get('customers', [ReportController::class, 'customers'])->name('customers');
        Route::get('cash', [ReportController::class, 'cash'])->name('cash');
        Route::get('export/{type}', [ReportController::class, 'export'])->name('export');
    });

    // Usuarios y Roles
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::post('users/{user}/reset-pin', [UserController::class, 'resetPin'])->name('users.reset-pin');

    Route::resource('roles', RoleController::class);

    // Configuración
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('general', [SettingsController::class, 'general'])->name('general');
        Route::post('general', [SettingsController::class, 'updateGeneral'])->name('general.update');

        Route::get('branches', [BranchController::class, 'index'])->name('branches');
        Route::resource('branches', BranchController::class)->except(['index']);

        Route::get('taxes', [TaxController::class, 'index'])->name('taxes');
        Route::resource('taxes', TaxController::class)->except(['index']);

        Route::get('resolutions', [ResolutionController::class, 'index'])->name('resolutions');
        Route::resource('resolutions', ResolutionController::class)->except(['index']);

        Route::get('printers', [PrinterController::class, 'index'])->name('printers');
        Route::resource('printers', PrinterController::class)->except(['index']);
        Route::post('printers/{printer}/test', [PrinterController::class, 'test'])->name('printers.test');

        Route::get('integrations', [SettingsController::class, 'integrations'])->name('integrations');
        Route::post('integrations', [SettingsController::class, 'updateIntegrations'])->name('integrations.update');

        Route::get('notifications', [SettingsController::class, 'notifications'])->name('notifications');
        Route::post('notifications', [SettingsController::class, 'updateNotifications'])->name('notifications.update');
    });

    // Auditoría
    Route::get('audit', [AuditController::class, 'index'])->name('audit.index');
    Route::get('audit/{log}', [AuditController::class, 'show'])->name('audit.show');
    Route::get('audit/export', [AuditController::class, 'export'])->name('audit.export');

    // Backups
    Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('backups', [BackupController::class, 'create'])->name('backups.create');
    Route::get('backups/{backup}/download', [BackupController::class, 'download'])->name('backups.download');
    Route::delete('backups/{backup}', [BackupController::class, 'destroy'])->name('backups.destroy');
});
