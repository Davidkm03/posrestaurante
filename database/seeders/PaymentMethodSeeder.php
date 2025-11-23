<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'code' => 'cash',
                'name' => 'Efectivo',
                'type' => 'cash',
                'dian_code' => '10',
                'requires_reference' => false,
                'opens_cash_drawer' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'credit_card',
                'name' => 'Tarjeta Crédito',
                'type' => 'credit_card',
                'dian_code' => '48',
                'requires_reference' => true,
                'opens_cash_drawer' => false,
                'sort_order' => 2,
            ],
            [
                'code' => 'debit_card',
                'name' => 'Tarjeta Débito',
                'type' => 'debit_card',
                'dian_code' => '49',
                'requires_reference' => true,
                'opens_cash_drawer' => false,
                'sort_order' => 3,
            ],
            [
                'code' => 'transfer',
                'name' => 'Transferencia',
                'type' => 'transfer',
                'dian_code' => 'ZZZ',
                'requires_reference' => true,
                'opens_cash_drawer' => false,
                'sort_order' => 4,
            ],
            [
                'code' => 'nequi',
                'name' => 'Nequi',
                'type' => 'transfer',
                'dian_code' => 'ZZZ',
                'requires_reference' => true,
                'opens_cash_drawer' => false,
                'sort_order' => 5,
            ],
            [
                'code' => 'daviplata',
                'name' => 'Daviplata',
                'type' => 'transfer',
                'dian_code' => 'ZZZ',
                'requires_reference' => true,
                'opens_cash_drawer' => false,
                'sort_order' => 6,
            ],
            [
                'code' => 'voucher',
                'name' => 'Bono / Vale',
                'type' => 'voucher',
                'dian_code' => 'ZZZ',
                'requires_reference' => true,
                'opens_cash_drawer' => false,
                'sort_order' => 7,
            ],
            [
                'code' => 'credit',
                'name' => 'Crédito (Fiado)',
                'type' => 'credit',
                'dian_code' => '30',
                'requires_reference' => false,
                'opens_cash_drawer' => false,
                'sort_order' => 8,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::create($method);
        }
    }
}
