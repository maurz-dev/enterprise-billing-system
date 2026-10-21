<?php

namespace Database\Seeders;

use App\Enums\DocType;
use App\Models\Branch;
use App\Models\DocumentSeries;
use Illuminate\Database\Seeder;

class DocumentSeriesSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::first();

        // F001: Facturas Electrónicas
        DocumentSeries::create([
            'branch_id' => $branch->id,
            'doc_type' => DocType::INVOICE,
            'series' => 'F001',
            'current_number' => 0,
            'is_active' => true,
        ]);

        // B001: Boletas de Venta Electrónicas
        DocumentSeries::create([
            'branch_id' => $branch->id,
            'doc_type' => DocType::BOLETA,
            'series' => 'B001',
            'current_number' => 0,
            'is_active' => true,
        ]);

        // FC01: Notas de Crédito para Facturas
        DocumentSeries::create([
            'branch_id' => $branch->id,
            'doc_type' => DocType::CREDIT_NOTE,
            'series' => 'FC01',
            'current_number' => 0,
            'is_active' => true,
        ]);
    }
}
