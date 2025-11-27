<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DIANResolution;
use App\Models\Branch;

class DIANResolutionSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::first();

        if (!$branch) {
            return;
        }

        // Resolution for invoices
        DIANResolution::firstOrCreate(
            [
                'branch_id' => $branch->id,
                'prefix' => 'FE',
                'document_type' => 'invoice',
            ],
            [
                'resolution_number' => '18764000001234',
                'resolution_date' => now()->subMonths(6),
                'range_from' => 1,
                'range_to' => 10000,
                'current_number' => 0,
                'technical_key' => 'fc8eac422eba16e22ffd8c6f94b3f40a6e38162c',
                'valid_from' => now()->subMonths(6),
                'valid_to' => now()->addMonths(18),
                'environment' => 'test',
                'is_active' => true,
                'is_contingency' => false,
            ]
        );

        // Resolution for credit notes
        DIANResolution::firstOrCreate(
            [
                'branch_id' => $branch->id,
                'prefix' => 'NC',
                'document_type' => 'credit_note',
            ],
            [
                'resolution_number' => '18764000001234',
                'resolution_date' => now()->subMonths(6),
                'range_from' => 1,
                'range_to' => 5000,
                'current_number' => 0,
                'technical_key' => 'fc8eac422eba16e22ffd8c6f94b3f40a6e38162c',
                'valid_from' => now()->subMonths(6),
                'valid_to' => now()->addMonths(18),
                'environment' => 'test',
                'is_active' => true,
                'is_contingency' => false,
            ]
        );

        // Resolution for debit notes
        DIANResolution::firstOrCreate(
            [
                'branch_id' => $branch->id,
                'prefix' => 'ND',
                'document_type' => 'debit_note',
            ],
            [
                'resolution_number' => '18764000001234',
                'resolution_date' => now()->subMonths(6),
                'range_from' => 1,
                'range_to' => 5000,
                'current_number' => 0,
                'technical_key' => 'fc8eac422eba16e22ffd8c6f94b3f40a6e38162c',
                'valid_from' => now()->subMonths(6),
                'valid_to' => now()->addMonths(18),
                'environment' => 'test',
                'is_active' => true,
                'is_contingency' => false,
            ]
        );
    }
}
