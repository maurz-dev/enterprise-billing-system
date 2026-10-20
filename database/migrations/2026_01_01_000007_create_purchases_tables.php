<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Reabastecimiento: al pasar a "received" genera movimientos de kardex (entrada).
        Schema::create('purchases', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->restrictOnDelete();
            $t->foreignId('branch_id')->constrained()->restrictOnDelete();
            $t->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $t->foreignId('supplier_id')->constrained('contacts')->restrictOnDelete();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->string('doc_type', 2)->nullable();      // comprobante del proveedor
            $t->string('doc_number', 30)->nullable();
            $t->string('status', 10)->default('draft'); // draft | received | voided
            $t->date('issue_date');
            $t->char('currency', 3)->default('PEN');
            $t->decimal('subtotal', 14, 2)->default(0);
            $t->decimal('tax_total', 14, 2)->default(0);
            $t->decimal('total', 14, 2)->default(0);
            $t->text('notes')->nullable();
            $t->timestamp('received_at')->nullable();
            $t->timestamps();

            $t->unique(['company_id', 'supplier_id', 'doc_type', 'doc_number'], 'uq_purchase_supplier_doc');
            $t->index(['company_id', 'status', 'issue_date']);
        });

        Schema::create('purchase_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_variant_id')->constrained()->restrictOnDelete();
            $t->decimal('quantity', 14, 4);
            $t->decimal('unit_cost', 14, 6);            // sin impuesto
            $t->decimal('tax_rate', 5, 2)->default(0);
            $t->decimal('tax_amount', 14, 2)->default(0);
            $t->decimal('total', 14, 2);
            $t->timestamps();

            $t->index('purchase_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
    }
};
