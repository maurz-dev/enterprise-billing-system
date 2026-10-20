<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Series y correlativos por sucursal (F001, B001, FC01...). Se incrementa con lockForUpdate().
        Schema::create('document_series', function (Blueprint $t) {
            $t->id();
            $t->foreignId('branch_id')->constrained()->restrictOnDelete();
            $t->string('doc_type', 2);                  // 01 factura, 03 boleta, 07 nota crédito, 08 nota débito
            $t->string('series', 4);
            $t->unsignedInteger('current_number')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();

            $t->unique(['branch_id', 'doc_type', 'series']);
        });

        Schema::create('quotes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->restrictOnDelete();
            $t->foreignId('branch_id')->constrained()->restrictOnDelete();
            $t->foreignId('contact_id')->constrained()->restrictOnDelete();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->string('number', 20);
            $t->string('status', 12)->default('draft'); // draft|sent|accepted|rejected|expired|converted
            $t->date('issue_date');
            $t->date('valid_until')->nullable();
            $t->char('currency', 3)->default('PEN');
            $t->decimal('exchange_rate', 10, 6)->default(1);
            $t->decimal('subtotal', 14, 2)->default(0);
            $t->decimal('discount_total', 14, 2)->default(0);
            $t->decimal('tax_total', 14, 2)->default(0);
            $t->decimal('total', 14, 2)->default(0);
            $t->text('notes')->nullable();
            $t->timestamps();

            $t->unique(['company_id', 'number']);
            $t->index(['company_id', 'status', 'issue_date']);
        });

        Schema::create('quote_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_variant_id')->nullable()->constrained()->restrictOnDelete();
            $t->string('description', 255);
            $t->decimal('quantity', 14, 4);
            $t->decimal('unit_price', 14, 4);           // sin impuesto
            $t->decimal('discount_amount', 14, 2)->default(0);
            $t->decimal('tax_rate', 5, 2)->default(0);
            $t->decimal('tax_amount', 14, 2)->default(0);
            $t->decimal('subtotal', 14, 2);
            $t->decimal('total', 14, 2);
            $t->timestamps();
        });

        // Comprobante (factura/boleta/notas). Nunca se borra: se anula (status = voided).
        Schema::create('invoices', function (Blueprint $t) {
            $t->id();
            $t->uuid('uuid')->unique();                 // identificador público en la API
            $t->foreignId('company_id')->constrained()->restrictOnDelete();
            $t->foreignId('branch_id')->constrained()->restrictOnDelete();
            $t->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $t->foreignId('contact_id')->constrained()->restrictOnDelete();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->foreignId('cash_session_id')->nullable()->constrained()->restrictOnDelete();
            $t->foreignId('quote_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('related_invoice_id')->nullable()->constrained('invoices')->restrictOnDelete(); // notas de crédito/débito
            $t->string('doc_type', 2);
            $t->string('series', 4)->nullable();        // NULL mientras sea borrador
            $t->unsignedInteger('number')->nullable();
            $t->string('status', 15)->default('draft'); // draft|issued|partially_paid|paid|voided
            $t->string('payment_condition', 10)->default('cash'); // cash | credit
            $t->date('issue_date');
            $t->date('due_date')->nullable();
            $t->char('currency', 3)->default('PEN');
            $t->decimal('exchange_rate', 10, 6)->default(1);
            $t->decimal('subtotal', 14, 2)->default(0);
            $t->decimal('discount_total', 14, 2)->default(0);
            $t->decimal('tax_total', 14, 2)->default(0);
            $t->decimal('total', 14, 2)->default(0);
            $t->decimal('paid_total', 14, 2)->default(0);
            $t->string('sunat_status', 15)->default('not_sent'); // not_sent|accepted|rejected
            $t->string('hash', 64)->nullable();
            $t->string('xml_path')->nullable();
            $t->string('cdr_path')->nullable();
            $t->text('notes')->nullable();
            $t->timestamp('issued_at')->nullable();
            $t->timestamp('voided_at')->nullable();
            $t->string('void_reason', 255)->nullable();
            $t->timestamps();

            $t->unique(['company_id', 'doc_type', 'series', 'number'], 'uq_invoice_correlative');
            $t->index(['company_id', 'status', 'issue_date']);
            $t->index(['contact_id', 'status']);
            $t->index('cash_session_id');
        });

        Schema::create('invoice_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_variant_id')->nullable()->constrained()->restrictOnDelete();
            $t->string('description', 255);
            $t->string('unit_code', 5)->default('NIU');
            $t->decimal('quantity', 14, 4);
            $t->decimal('unit_price', 14, 4);           // sin impuesto
            $t->decimal('discount_amount', 14, 2)->default(0);
            $t->foreignId('tax_id')->nullable()->constrained('taxes')->restrictOnDelete();
            $t->decimal('tax_rate', 5, 2)->default(0);  // snapshot de la tasa al emitir
            $t->decimal('tax_amount', 14, 2)->default(0);
            $t->decimal('subtotal', 14, 2);
            $t->decimal('total', 14, 2);
            $t->decimal('unit_cost', 14, 6)->default(0); // snapshot del costo para margen
            $t->timestamps();

            $t->index('invoice_id');
            $t->index('product_variant_id');
        });

        Schema::create('invoice_payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('invoice_id')->constrained()->restrictOnDelete();
            $t->foreignId('payment_method_id')->constrained()->restrictOnDelete();
            $t->foreignId('cash_session_id')->nullable()->constrained()->restrictOnDelete();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->decimal('amount', 14, 2);
            $t->string('reference', 100)->nullable();   // nº operación / id de pasarela
            $t->timestamp('paid_at')->useCurrent();
            $t->timestamp('voided_at')->nullable();
            $t->timestamps();

            $t->index(['invoice_id', 'voided_at']);
            $t->index(['cash_session_id', 'payment_method_id']);
        });

        DB::statement("ALTER TABLE invoices ADD CONSTRAINT chk_invoice_status CHECK (status IN ('draft','issued','partially_paid','paid','voided'))");
        DB::statement("ALTER TABLE invoices ADD CONSTRAINT chk_invoice_amounts CHECK (total >= 0 AND paid_total >= 0 AND paid_total <= total)");
        DB::statement("ALTER TABLE invoice_items ADD CONSTRAINT chk_item_qty CHECK (quantity > 0 AND unit_price >= 0)");
        DB::statement("ALTER TABLE invoice_payments ADD CONSTRAINT chk_payment_amt CHECK (amount > 0)");
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('quote_items');
        Schema::dropIfExists('quotes');
        Schema::dropIfExists('document_series');
    }
};
