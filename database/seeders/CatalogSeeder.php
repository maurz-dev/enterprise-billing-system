<?php

namespace Database\Seeders;

use App\Enums\StockMovementDirection;
use App\Enums\StockMovementReason;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        $warehouse = Warehouse::first();
        $inventoryService = app(InventoryService::class);

        // Impuesto IGV 18%
        $taxIgv = Tax::create([
            'company_id' => $company->id,
            'code' => 'IGV',
            'name' => 'Impuesto General a las Ventas (18%)',
            'rate' => 18.00,
            'is_default' => true,
        ]);

        // Unidades SUNAT
        $unitNiu = Unit::create(['code' => 'NIU', 'name' => 'Unidad / Pieza']);
        $unitKgm = Unit::create(['code' => 'KGM', 'name' => 'Kilogramos']);

        // Categorías
        $catBebidas = Category::create(['company_id' => $company->id, 'name' => 'Bebidas y Licores']);
        $catAbarrotes = Category::create(['company_id' => $company->id, 'name' => 'Insumos y Abarrotes']);

        // 1. Producto Simple
        $p1 = Product::create([
            'company_id' => $company->id,
            'category_id' => $catAbarrotes->id,
            'unit_id' => $unitKgm->id,
            'tax_id' => $taxIgv->id,
            'name' => 'Aceite Vegetal Premium Primor 1L',
            'description' => 'Aceite 100% puro de soya y girasol para alta cocina y fritura',
            'type' => 'goods',
            'has_variants' => false,
            'tracks_inventory' => true,
            'price_includes_tax' => true,
            'is_active' => true,
        ]);

        $v1 = ProductVariant::create([
            'company_id' => $company->id,
            'product_id' => $p1->id,
            'sku' => 'ACE-PRIM-1L',
            'barcode' => '7750123456789',
            'name' => 'Botella 1 Litro',
            'purchase_price' => 7.20,
            'sale_price' => 9.50,
            'min_stock' => 20,
            'is_default' => true,
            'is_active' => true,
        ]);

        // Carga de Inventario Inicial en Kardex
        $inventoryService->recordMovement(
            companyId: $company->id,
            warehouseId: $warehouse->id,
            productVariantId: $v1->id,
            direction: StockMovementDirection::IN,
            reason: StockMovementReason::INITIAL,
            quantity: 150.0,
            unitCost: 7.20,
            notes: 'Inventario inicial de apertura de almacén'
        );

        // 2. Producto con Variantes
        $p2 = Product::create([
            'company_id' => $company->id,
            'category_id' => $catBebidas->id,
            'unit_id' => $unitNiu->id,
            'tax_id' => $taxIgv->id,
            'name' => 'Gaseosa Inca Kola Original',
            'description' => 'Bebida gasificada sabor nacional',
            'type' => 'goods',
            'has_variants' => true,
            'tracks_inventory' => true,
            'price_includes_tax' => true,
            'is_active' => true,
        ]);

        $v2a = ProductVariant::create([
            'company_id' => $company->id,
            'product_id' => $p2->id,
            'sku' => 'IK-500ML',
            'barcode' => '7751111222333',
            'name' => 'Personal 500ml',
            'attributes' => ['tamaño' => '500ml', 'empaque' => 'Pet'],
            'purchase_price' => 2.10,
            'sale_price' => 3.50,
            'min_stock' => 50,
            'is_default' => true,
            'is_active' => true,
        ]);

        $v2b = ProductVariant::create([
            'company_id' => $company->id,
            'product_id' => $p2->id,
            'sku' => 'IK-1.5L',
            'barcode' => '7751111222444',
            'name' => 'Familiar 1.5 Litros',
            'attributes' => ['tamaño' => '1.5L', 'empaque' => 'Pet'],
            'purchase_price' => 4.50,
            'sale_price' => 7.00,
            'min_stock' => 30,
            'is_default' => false,
            'is_active' => true,
        ]);

        $inventoryService->recordMovement(
            companyId: $company->id,
            warehouseId: $warehouse->id,
            productVariantId: $v2a->id,
            direction: StockMovementDirection::IN,
            reason: StockMovementReason::INITIAL,
            quantity: 200.0,
            unitCost: 2.10,
            notes: 'Stock inicial 500ml'
        );

        $inventoryService->recordMovement(
            companyId: $company->id,
            warehouseId: $warehouse->id,
            productVariantId: $v2b->id,
            direction: StockMovementDirection::IN,
            reason: StockMovementReason::INITIAL,
            quantity: 80.0,
            unitCost: 4.50,
            notes: 'Stock inicial 1.5L'
        );
    }
}
