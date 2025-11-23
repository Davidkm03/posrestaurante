<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Resetear cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            // POS
            'pos.access',
            'pos.discount',
            'pos.cancel_order',
            'pos.reprint',

            // Órdenes
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.delete',
            'orders.cancel',
            'orders.discount',
            'orders.courtesy',

            // Pagos
            'payments.process',
            'payments.refund',
            'payments.view',

            // Caja
            'cash.open',
            'cash.close',
            'cash.movements',
            'cash.view',
            'cash.withdraw',

            // Productos
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',

            // Categorías
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',

            // Mesas
            'tables.view',
            'tables.create',
            'tables.edit',
            'tables.delete',
            'tables.assign',

            // Clientes
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',

            // Facturas
            'invoices.view',
            'invoices.create',
            'invoices.void',
            'invoices.download',

            // Inventario
            'inventory.view',
            'inventory.adjust',
            'inventory.purchase',

            // Reportes
            'reports.view',
            'reports.sales',
            'reports.inventory',
            'reports.taxes',
            'reports.export',

            // Usuarios
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Configuración
            'settings.view',
            'settings.edit',
            'settings.dian',

            // Cocina
            'kitchen.access',
            'kitchen.update',

            // Delivery
            'delivery.access',
            'delivery.assign',

            // Auditoría
            'audit.view',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles y asignar permisos
        $superAdmin = Role::create(['name' => UserRole::SUPER_ADMIN->value]);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::create(['name' => UserRole::ADMIN->value]);
        $admin->givePermissionTo([
            'pos.access', 'pos.discount', 'pos.cancel_order', 'pos.reprint',
            'orders.view', 'orders.create', 'orders.edit', 'orders.delete', 'orders.cancel', 'orders.discount',
            'payments.process', 'payments.refund', 'payments.view',
            'cash.open', 'cash.close', 'cash.movements', 'cash.view', 'cash.withdraw',
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            'tables.view', 'tables.create', 'tables.edit', 'tables.delete', 'tables.assign',
            'customers.view', 'customers.create', 'customers.edit', 'customers.delete',
            'invoices.view', 'invoices.create', 'invoices.void', 'invoices.download',
            'inventory.view', 'inventory.adjust', 'inventory.purchase',
            'reports.view', 'reports.sales', 'reports.inventory', 'reports.taxes', 'reports.export',
            'users.view',
            'settings.view',
            'kitchen.access',
            'audit.view',
        ]);

        $manager = Role::create(['name' => UserRole::MANAGER->value]);
        $manager->givePermissionTo([
            'pos.access', 'pos.discount', 'pos.cancel_order', 'pos.reprint',
            'orders.view', 'orders.create', 'orders.edit', 'orders.cancel', 'orders.discount',
            'payments.process', 'payments.view',
            'cash.open', 'cash.close', 'cash.movements', 'cash.view',
            'products.view',
            'categories.view',
            'tables.view', 'tables.assign',
            'customers.view', 'customers.create', 'customers.edit',
            'invoices.view', 'invoices.create', 'invoices.download',
            'inventory.view',
            'reports.view', 'reports.sales',
            'kitchen.access',
        ]);

        $cashier = Role::create(['name' => UserRole::CASHIER->value]);
        $cashier->givePermissionTo([
            'pos.access', 'pos.reprint',
            'orders.view', 'orders.create', 'orders.edit',
            'payments.process', 'payments.view',
            'cash.open', 'cash.close', 'cash.view',
            'tables.view',
            'customers.view', 'customers.create',
            'invoices.view', 'invoices.create', 'invoices.download',
        ]);

        $waiter = Role::create(['name' => UserRole::WAITER->value]);
        $waiter->givePermissionTo([
            'pos.access',
            'orders.view', 'orders.create', 'orders.edit',
            'tables.view', 'tables.assign',
            'customers.view',
        ]);

        $kitchen = Role::create(['name' => UserRole::KITCHEN->value]);
        $kitchen->givePermissionTo([
            'kitchen.access', 'kitchen.update',
            'orders.view',
        ]);

        $delivery = Role::create(['name' => UserRole::DELIVERY->value]);
        $delivery->givePermissionTo([
            'delivery.access',
            'orders.view',
        ]);

        $accountant = Role::create(['name' => UserRole::ACCOUNTANT->value]);
        $accountant->givePermissionTo([
            'invoices.view', 'invoices.download',
            'reports.view', 'reports.sales', 'reports.inventory', 'reports.taxes', 'reports.export',
            'cash.view',
            'audit.view',
        ]);
    }
}
