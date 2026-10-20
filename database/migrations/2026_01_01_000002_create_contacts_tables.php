<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Clientes y proveedores comparten tabla: una misma persona puede ser ambos.
        Schema::create('contacts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->restrictOnDelete();
            $t->boolean('is_customer')->default(true);
            $t->boolean('is_supplier')->default(false);
            $t->string('doc_type', 10);            // DNI, RUC, CE, PASSPORT, NIF, NONE
            $t->string('doc_number', 20);
            $t->string('legal_name', 200);
            $t->string('trade_name', 200)->nullable();
            $t->string('email', 150)->nullable();
            $t->string('phone', 30)->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();

            $t->unique(['company_id', 'doc_type', 'doc_number']);
            $t->index(['company_id', 'legal_name']);
        });

        Schema::create('contact_addresses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $t->string('type', 10)->default('fiscal'); // fiscal | delivery
            $t->string('line', 255);
            $t->string('ubigeo', 6)->nullable();
            $t->string('district', 100)->nullable();
            $t->string('province', 100)->nullable();
            $t->string('department', 100)->nullable();
            $t->char('country', 2)->default('PE');
            $t->boolean('is_default')->default(false);
            $t->timestamps();

            $t->index(['contact_id', 'type']);
        });

        DB::statement("ALTER TABLE contacts ADD CONSTRAINT chk_contacts_role CHECK (is_customer OR is_supplier)");
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_addresses');
        Schema::dropIfExists('contacts');
    }
};
