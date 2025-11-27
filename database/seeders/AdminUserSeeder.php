<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear sucursal principal
        $branch = Branch::create([
            'name' => 'Sucursal Principal',
            'code' => 'SUC001',
            'address' => 'Dirección del restaurante',
            'city' => 'Bogotá',
            'department' => 'Cundinamarca',
            'country' => 'Colombia',
            'phone' => '3001234567',
            'email' => 'contacto@restaurante.com',
            'is_main' => true,
            'is_active' => true,
            'settings' => [
                'currency' => 'COP',
                'timezone' => 'America/Bogota',
                'tax_included' => true,
                'tip_percentage' => 10,
                'receipt_footer' => 'Gracias por su visita',
            ],
        ]);

        // Crear usuario administrador
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@posrestaurante.com',
            'password' => Hash::make('password'),
            'pin' => '1234',
            'position' => 'Administrador',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $admin->assignRole(UserRole::SUPER_ADMIN->value);
        $admin->branches()->attach($branch->id, ['is_default' => true]);

        // Crear usuarios de ejemplo para cada rol
        $users = [
            [
                'name' => 'Gerente Demo',
                'email' => 'gerente@pos.com',
                'pin' => '2345',
                'role' => UserRole::MANAGER->value,
                'position' => 'Gerente',
            ],
            [
                'name' => 'Cajero Demo',
                'email' => 'cajero@pos.com',
                'pin' => '3456',
                'role' => UserRole::CASHIER->value,
                'position' => 'Cajero',
            ],
            [
                'name' => 'Mesero Demo',
                'email' => 'mesero@pos.com',
                'pin' => '4567',
                'role' => UserRole::WAITER->value,
                'position' => 'Mesero',
            ],
            [
                'name' => 'Cocina Demo',
                'email' => 'cocina@pos.com',
                'pin' => '5678',
                'role' => UserRole::KITCHEN->value,
                'position' => 'Cocina',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('password'),
                'pin' => $userData['pin'],
                'position' => $userData['position'],
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            $user->assignRole($userData['role']);
            $user->branches()->attach($branch->id, ['is_default' => true]);
        }
    }
}
