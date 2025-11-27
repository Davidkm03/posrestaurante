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

    public function description(): string
    {
        return match($this) {
            self::SUPER_ADMIN => 'Acceso total al sistema',
            self::ADMIN => 'Gestión completa de la sucursal',
            self::MANAGER => 'Supervisión de operaciones y reportes',
            self::CASHIER => 'Procesar pagos y manejar caja',
            self::WAITER => 'Tomar pedidos y atender mesas',
            self::KITCHEN => 'Ver y gestionar pedidos de cocina',
            self::DELIVERY => 'Gestión de entregas a domicilio',
            self::ACCOUNTANT => 'Acceso a reportes financieros',
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
