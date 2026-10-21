<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Contact;
use App\Models\ContactAddress;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();

        // 1. Cliente Corporativo (RUC)
        $clientRuc = Contact::create([
            'company_id' => $company->id,
            'is_customer' => true,
            'is_supplier' => false,
            'doc_type' => 'RUC',
            'doc_number' => '20554433221',
            'legal_name' => 'CORPORACIÓN GASTRONÓMICA DEL SUR S.A.C.',
            'trade_name' => 'Restaurantes El Gaucho',
            'email' => 'facturacion@elgaucho.pe',
            'phone' => '+51 987654321',
            'is_active' => true,
        ]);

        ContactAddress::create([
            'contact_id' => $clientRuc->id,
            'type' => 'fiscal',
            'line' => 'Calle Las Begonias 441, San Isidro',
            'ubigeo' => '150131',
            'province' => 'Lima',
            'department' => 'Lima',
            'country' => 'PE',
            'is_default' => true,
        ]);

        // 2. Cliente Persona Natural (DNI)
        $clientDni = Contact::create([
            'company_id' => $company->id,
            'is_customer' => true,
            'is_supplier' => false,
            'doc_type' => 'DNI',
            'doc_number' => '72819203',
            'legal_name' => 'CARLOS ALBERTO MENDOZA RÍOS',
            'email' => 'carlos.mendoza@gmail.com',
            'phone' => '+51 991234567',
            'is_active' => true,
        ]);

        ContactAddress::create([
            'contact_id' => $clientDni->id,
            'type' => 'delivery',
            'line' => 'Av. Benavides 1230, Dpto 402, Miraflores',
            'ubigeo' => '150122',
            'province' => 'Lima',
            'department' => 'Lima',
            'country' => 'PE',
            'is_default' => true,
        ]);

        // 3. Proveedor Mayorista
        $supplier = Contact::create([
            'company_id' => $company->id,
            'is_customer' => false,
            'is_supplier' => true,
            'doc_type' => 'RUC',
            'doc_number' => '20100055201',
            'legal_name' => 'ALICORP S.A.A.',
            'trade_name' => 'Alicorp Mayorista',
            'email' => 'pedidos@alicorp.com.pe',
            'phone' => '+51 1 315-0800',
            'is_active' => true,
        ]);

        ContactAddress::create([
            'contact_id' => $supplier->id,
            'type' => 'fiscal',
            'line' => 'Av. Argentina 4793, Carmen de la Legua, Callao',
            'ubigeo' => '070101',
            'province' => 'Callao',
            'department' => 'Callao',
            'country' => 'PE',
            'is_default' => true,
        ]);
    }
}
