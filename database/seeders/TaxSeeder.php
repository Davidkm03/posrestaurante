<?php

namespace Database\Seeders;

use App\Models\Tax;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    public function run(): void
    {
        $taxes = [
            [
                'code' => 'IVA_19',
                'name' => 'IVA 19%',
                'dian_code' => '01',
                'percentage' => 19.00,
            ],
            [
                'code' => 'IVA_5',
                'name' => 'IVA 5%',
                'dian_code' => '01',
                'percentage' => 5.00,
            ],
            [
                'code' => 'IVA_0',
                'name' => 'IVA 0% (Exento)',
                'dian_code' => '01',
                'percentage' => 0.00,
            ],
            [
                'code' => 'INC_8',
                'name' => 'Impoconsumo 8%',
                'dian_code' => '04',
                'percentage' => 8.00,
            ],
            [
                'code' => 'EXCLUIDO',
                'name' => 'Excluido de IVA',
                'dian_code' => 'ZZ',
                'percentage' => 0.00,
            ],
        ];

        foreach ($taxes as $tax) {
            Tax::create($tax);
        }
    }
}
