<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\CashRegister;
use App\Models\Company;
use App\Models\PaymentMethod;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::create([
            'legal_name' => 'DISTRIBUIDORA & LOGÍSTICA LOS ANDES S.A.C.',
            'trade_name' => 'Los Andes Distribuciones',
            'tax_id_type' => 'RUC',
            'tax_id' => '20601234567',
            'fiscal_address' => 'Av. Javier Prado Este 4500, Oficina 802, Santiago de Surco',
            'ubigeo' => '150140',
            'country' => 'PE',
            'currency' => 'PEN',
            'email' => 'ventas@losandeslogistica.com',
            'phone' => '+51 1 710-5000',
            'is_active' => true,
        ]);

        $branch = Branch::create([
            'company_id' => $company->id,
            'code' => 'B01',
            'name' => 'Sede Central Miraflores',
            'address' => 'Av. José Pardo 601, Miraflores, Lima',
            'ubigeo' => '150122',
            'is_main' => true,
            'is_active' => true,
        ]);

        Warehouse::create([
            'branch_id' => $branch->id,
            'code' => 'ALM-01',
            'name' => 'Almacén Principal de Despacho',
            'is_active' => true,
        ]);

        CashRegister::create([
            'branch_id' => $branch->id,
            'name' => 'Caja Principal Ventas 01',
            'is_active' => true,
        ]);

        // Payment Methods
        PaymentMethod::create(['company_id' => $company->id, 'code' => 'CASH', 'name' => 'Efectivo', 'type' => 'cash']);
        PaymentMethod::create(['company_id' => $company->id, 'code' => 'YAPE', 'name' => 'Yape / Plin', 'type' => 'gateway']);
        PaymentMethod::create(['company_id' => $company->id, 'code' => 'TRANSFER', 'name' => 'Transferencia BCP', 'type' => 'transfer']);
        PaymentMethod::create(['company_id' => $company->id, 'code' => 'CARD', 'name' => 'Tarjeta Débito/Crédito (POS)', 'type' => 'card']);
    }
}
