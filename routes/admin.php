<?php

use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CashController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ComboController;
use App\Http\Controllers\Admin\CreditNoteController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DebitNoteController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\LoyaltyController;
use App\Http\Controllers\Admin\ModifierController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PrintController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\FactusController;
use App\Http\Controllers\Admin\RappiController;
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
use App\Http\Middleware\SetCurrentBranch;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', SetCurrentBranch::class, 'check.role:administrador,gerente'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Productos y Menú
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/toggle-active', [ProductController::class, 'toggleActive'])->name('products.toggle-active');
    Route::post('products/{product}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');

    Route::resource('categories', CategoryController::class);
    Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
    Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

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

    // Órdenes
    Route::resource('orders', OrderController::class);
    Route::get('orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('orders/daily-report', [OrderController::class, 'dailyReport'])->name('orders.daily-report');

    // Facturación
    Route::resource('invoices', InvoiceController::class)->only(['index', 'show']);
    Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::get('invoices/{invoice}/xml', [InvoiceController::class, 'xml'])->name('invoices.xml');
    Route::post('invoices/{invoice}/resend', [InvoiceController::class, 'resend'])->name('invoices.resend');
    Route::post('invoices/{invoice}/send-email', [InvoiceController::class, 'sendEmail'])->name('invoices.send-email');
    Route::patch('invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');

    Route::resource('credit-notes', CreditNoteController::class);
    Route::resource('debit-notes', DebitNoteController::class);

    // Caja
    Route::get('cash', [CashController::class, 'index'])->name('cash.index');
    Route::get('cash/{cashSession}', [CashController::class, 'show'])->name('cash.show');
    Route::post('cash/open', [CashController::class, 'open'])->name('cash.open');
    Route::post('cash/{cashSession}/close', [CashController::class, 'close'])->name('cash.close');
    Route::get('cash-sessions', [CashController::class, 'sessions'])->name('cash.sessions');
    Route::get('cash-sessions/{session}', [CashController::class, 'showSession'])->name('cash.sessions.show');
    Route::get('cash-sessions/{session}/report', [CashController::class, 'sessionReport'])->name('cash.sessions.report');

    // Inventario
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('movements', [InventoryController::class, 'movements'])->name('movements');
        Route::get('low-stock', [InventoryController::class, 'lowStock'])->name('low-stock');
        Route::get('valuation', [InventoryController::class, 'valuation'])->name('valuation');
        Route::get('{product}', [InventoryController::class, 'show'])->name('show');
        Route::get('{product}/adjust', [InventoryController::class, 'adjustForm'])->name('adjust');
        Route::post('{product}/adjust', [InventoryController::class, 'adjust'])->name('adjust.store');
        Route::post('{product}/waste', [InventoryController::class, 'waste'])->name('waste');
    });

    // Proveedores y Compras
    Route::resource('suppliers', SupplierController::class);
    Route::resource('purchases', PurchaseController::class);
    Route::post('purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');

    // Promociones
    Route::resource('promotions', PromotionController::class);
    Route::post('promotions/{promotion}/toggle-active', [PromotionController::class, 'toggleActive'])->name('promotions.toggle-active');

    // Programa de Lealtad
    Route::prefix('loyalty')->name('loyalty.')->group(function () {
        Route::get('/', [LoyaltyController::class, 'index'])->name('index');
        Route::get('settings', [LoyaltyController::class, 'settings'])->name('settings');
        Route::put('settings', [LoyaltyController::class, 'updateSettings'])->name('update-settings');
        Route::get('export', [LoyaltyController::class, 'export'])->name('export');
        Route::post('expire-points', [LoyaltyController::class, 'expirePoints'])->name('expire-points');
        Route::get('customer/{customer}', [LoyaltyController::class, 'customerHistory'])->name('customer-history');
        Route::post('customer/{customer}/adjust', [LoyaltyController::class, 'adjustPoints'])->name('adjust-points');
    });

    // Reportes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('products', [ReportController::class, 'products'])->name('products');
        Route::get('waiters', [ReportController::class, 'waiters'])->name('waiters');
        Route::get('hourly', [ReportController::class, 'hourly'])->name('hourly');
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
        Route::get('branches/{branch}/settings', [BranchController::class, 'settings'])->name('branches.settings');
        Route::put('branches/{branch}/settings', [BranchController::class, 'updateSettings'])->name('branches.update-settings');
        Route::get('branches/{branch}/hours', [BranchController::class, 'hours'])->name('branches.hours');
        Route::put('branches/{branch}/hours', [BranchController::class, 'updateHours'])->name('branches.update-hours');
        Route::get('branches/{branch}/users', [BranchController::class, 'users'])->name('branches.users');
        Route::post('branches/{branch}/users', [BranchController::class, 'addUser'])->name('branches.add-user');
        Route::delete('branches/{branch}/users/{user}', [BranchController::class, 'removeUser'])->name('branches.remove-user');

        Route::get('taxes', [TaxController::class, 'index'])->name('taxes');
        Route::resource('taxes', TaxController::class)->except(['index']);

        Route::get('resolutions', [ResolutionController::class, 'index'])->name('resolutions');
        Route::resource('resolutions', ResolutionController::class)->except(['index']);

        Route::get('printers', [PrintController::class, 'index'])->name('printers');
        Route::resource('printers', PrintController::class)->except(['index']);
        Route::post('printers/{printer}/test', [PrintController::class, 'test'])->name('printers.test');
        Route::post('print/receipt/{order}', [PrintController::class, 'printReceipt'])->name('print.receipt');
        Route::post('print/kitchen/{order}', [PrintController::class, 'printKitchenTicket'])->name('print.kitchen');
        Route::post('print/cash-close/{session}', [PrintController::class, 'printCashClose'])->name('print.cash-close');

        Route::get('integrations', [SettingsController::class, 'integrations'])->name('integrations');
        Route::post('integrations', [SettingsController::class, 'updateIntegrations'])->name('integrations.update');

        Route::get('notifications', [SettingsController::class, 'notifications'])->name('notifications');
        Route::post('notifications', [SettingsController::class, 'updateNotifications'])->name('notifications.update');
    });

    // Integraciones Rappi
    Route::prefix('integrations')->name('integrations.')->group(function () {
        Route::prefix('rappi')->name('rappi.')->group(function () {
            Route::post('connect', [RappiController::class, 'connect'])->name('connect');
            Route::post('disconnect', [RappiController::class, 'disconnect'])->name('disconnect');
            Route::post('sync', [RappiController::class, 'sync'])->name('sync');
            Route::post('sync-menu', [RappiController::class, 'syncMenu'])->name('sync-menu');
            Route::post('orders/{order}/accept', [RappiController::class, 'acceptOrder'])->name('orders.accept');
            Route::post('orders/{order}/reject', [RappiController::class, 'rejectOrder'])->name('orders.reject');
            Route::post('orders/{order}/ready', [RappiController::class, 'readyForPickup'])->name('orders.ready');
        });

        // Integración Factus (Facturación Electrónica)
        Route::prefix('factus')->name('factus.')->group(function () {
            Route::post('test', [FactusController::class, 'test'])->name('test');
            Route::post('sync', [FactusController::class, 'sync'])->name('sync');
            Route::post('logout', [FactusController::class, 'logout'])->name('logout');
            Route::get('token-status', [FactusController::class, 'tokenStatus'])->name('token-status');
            Route::post('invoice/{invoice}', [FactusController::class, 'createInvoice'])->name('invoice.create');
            Route::get('invoice/{invoiceId}/pdf', [FactusController::class, 'downloadPdf'])->name('invoice.pdf');
            Route::get('invoice/{invoiceId}/xml', [FactusController::class, 'downloadXml'])->name('invoice.xml');
            Route::get('test-invoice', [FactusController::class, 'testInvoice'])->name('test-invoice');
        });
    });

    // Auditoría
    Route::get('audit', [AuditController::class, 'index'])->name('audit.index');
    Route::get('audit/{log}', [AuditController::class, 'show'])->name('audit.show');
    Route::get('audit/export', [AuditController::class, 'export'])->name('audit.export');

    // Backups
    Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('backups', [BackupController::class, 'create'])->name('backups.create');
    Route::get('backups/{backup}/download', [BackupController::class, 'download'])->name('backups.download');
    Route::post('backups/{backup}/restore', [BackupController::class, 'restore'])->name('backups.restore');
    Route::delete('backups/{backup}', [BackupController::class, 'destroy'])->name('backups.destroy');
});
