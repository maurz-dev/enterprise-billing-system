<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->restrictOnDelete();
            $t->string('code', 10);                 // IGV, IVA, EXO, INA
            $t->string('name', 100);
            $t->decimal('rate', 5, 2);              // 18.00
            $t->boolean('is_default')->default(false);
            $t->timestamps();

            $t->unique(['company_id', 'code']);
        });

        Schema::create('units', function (Blueprint $t) {
            $t->id();
            $t->string('code', 5)->unique();        // NIU, ZZ, KGM (catálogo SUNAT 03)
            $t->string('name', 60);
        });

        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->restrictOnDelete();
            $t->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $t->string('name', 120);
            $t->timestamps();

            $t->unique(['company_id', 'parent_id', 'name']);
        });

        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->restrictOnDelete();
            $t->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $t->foreignId('tax_id')->constrained('taxes')->restrictOnDelete();
            $t->string('name', 200);
            $t->text('description')->nullable();
            $t->string('type', 10)->default('goods');   // goods | service
            $t->boolean('has_variants')->default(false);
            $t->boolean('tracks_inventory')->default(true);
            $t->boolean('price_includes_tax')->default(true);
            $t->boolean('is_active')->default(true);
            $t->timestamps();

            $t->index(['company_id', 'name']);
        });

        // Todo producto tiene >= 1 variante. Un producto simple = 1 variante "default".
        Schema::create('product_variants', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->restrictOnDelete(); // desnormalizado para unicidad de SKU
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->string('sku', 60);
            $t->string('barcode', 60)->nullable();
            $t->string('name', 150)->nullable();         // "Rojo / M"
            $t->json('attributes')->nullable();          // {"color":"Rojo","talla":"M"}
            $t->decimal('purchase_price', 14, 4)->default(0);
            $t->decimal('sale_price', 14, 4)->default(0);
            $t->decimal('min_stock', 14, 4)->default(0);
            $t->boolean('is_default')->default(false);
            $t->boolean('is_active')->default(true);
            $t->timestamps();

            $t->unique(['company_id', 'sku']);
            $t->index(['company_id', 'barcode']);
            $t->index('product_id');
        });

        DB::statement("ALTER TABLE taxes ADD CONSTRAINT chk_taxes_rate CHECK (rate >= 0 AND rate <= 100)");
        DB::statement("ALTER TABLE product_variants ADD CONSTRAINT chk_variants_prices CHECK (purchase_price >= 0 AND sale_price >= 0)");
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('units');
        Schema::dropIfExists('taxes');
    }
};
