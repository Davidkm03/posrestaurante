<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case CASHIER = 'cashier';
    case WAITER = 'waiter';
    case KITCHEN = 'kitchen';
    case DELIVERY = 'delivery';
    case ACCOUNTANT = 'accountant';

    public function label(): string
    {
        return match($this) {
            self::SUPER_ADMIN => 'Super Administrador',
            self::ADMIN => 'Administrador',
            self::MANAGER => 'Gerente',
            self::CASHIER => 'Cajero',
            self::WAITER => 'Mesero',
            self::KITCHEN => 'Cocina',
            self::DELIVERY => 'Domiciliario',
            self::ACCOUNTANT => 'Contador',
        };
    }

    public function defaultPermissions(): array
    {
        return match($this) {
            self::SUPER_ADMIN => ['*'],
            self::ADMIN => [
                'pos.access', 'orders.*', 'products.*', 'categories.*',
                'tables.*', 'users.view', 'reports.*', 'cash.*',
                'invoices.*', 'inventory.*', 'settings.view',
            ],
            self::MANAGER => [
                'pos.access', 'orders.*', 'products.view', 'tables.*',
                'reports.view', 'cash.*', 'invoices.view', 'inventory.view',
            ],
            self::CASHIER => [
                'pos.access', 'orders.create', 'orders.edit', 'orders.view',
                'payments.process', 'cash.open', 'cash.close', 'invoices.create',
            ],
            self::WAITER => [
                'pos.access', 'orders.create', 'orders.edit', 'orders.view',
                'tables.view', 'tables.assign',
            ],
            self::KITCHEN => [
                'kitchen.access', 'orders.view', 'orders.update_status',
            ],
            self::DELIVERY => [
                'delivery.access', 'orders.view', 'orders.update_status',
            ],
            self::ACCOUNTANT => [
                'reports.*', 'invoices.view', 'cash.view',
            ],
        };
    }
}
