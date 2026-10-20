<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Saldo actual por variante y almacén (costo promedio ponderado).
        Schema::create('stocks', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_variant_id')->constrained()->restrictOnDelete();
            $t->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $t->decimal('quantity', 14, 4)->default(0);
            $t->decimal('avg_cost', 14, 6)->default(0);
            $t->timestamps();

            $t->unique(['product_variant_id', 'warehouse_id']);
            $t->index('warehouse_id');
        });

        // Kardex: libro inmutable (solo INSERT). Cada fila guarda el saldo resultante.
        Schema::create('stock_movements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->restrictOnDelete();
            $t->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $t->foreignId('product_variant_id')->constrained()->restrictOnDelete();
            $t->string('direction', 3);                 // in | out
            $t->string('reason', 20);                   // initial, purchase, sale, return, adjustment, transfer, void
            $t->decimal('quantity', 14, 4);             // siempre positiva
            $t->decimal('unit_cost', 14, 6);
            $t->decimal('total_cost', 18, 6);
            $t->decimal('balance_quantity', 14, 4);
            $t->decimal('balance_avg_cost', 14, 6);
            $t->nullableMorphs('reference');            // Invoice, Purchase, ...
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('notes', 255)->nullable();
            $t->timestamp('occurred_at')->useCurrent();
            $t->timestamps();

            $t->index(['product_variant_id', 'warehouse_id', 'occurred_at', 'id'], 'idx_kardex_lookup');
            $t->index(['company_id', 'occurred_at']);
        });

        DB::statement("ALTER TABLE stocks ADD CONSTRAINT chk_stocks_qty CHECK (quantity >= 0)");
        DB::statement("ALTER TABLE stock_movements ADD CONSTRAINT chk_movements_qty CHECK (quantity > 0 AND unit_cost >= 0)");
        DB::statement("ALTER TABLE stock_movements ADD CONSTRAINT chk_movements_dir CHECK (direction IN ('in','out'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stocks');
    }
};
