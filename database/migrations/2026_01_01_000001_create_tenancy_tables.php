<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $t) {
            $t->id();
            $t->string('legal_name', 200);
            $t->string('trade_name', 200)->nullable();
            $t->string('tax_id_type', 10)->default('RUC');
            $t->string('tax_id', 20)->unique();
            $t->string('fiscal_address', 255);
            $t->string('ubigeo', 6)->nullable();
            $t->char('country', 2)->default('PE');
            $t->char('currency', 3)->default('PEN');
            $t->string('email', 150)->nullable();
            $t->string('phone', 30)->nullable();
            $t->string('logo_path')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->softDeletes();
        });

        Schema::create('branches', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->restrictOnDelete();
            $t->string('code', 10);
            $t->string('name', 150);
            $t->string('address', 255)->nullable();
            $t->string('ubigeo', 6)->nullable();
            $t->boolean('is_main')->default(false);
            $t->boolean('is_active')->default(true);
            $t->timestamps();

            $t->unique(['company_id', 'code']);
        });

        Schema::create('warehouses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('branch_id')->constrained()->restrictOnDelete();
            $t->string('code', 10);
            $t->string('name', 150);
            $t->boolean('is_active')->default(true);
            $t->timestamps();

            $t->unique(['branch_id', 'code']);
        });

        // Usuarios ligados a empresa/sucursal. Superadmin => company_id NULL.
        Schema::table('users', function (Blueprint $t) {
            $t->foreignId('company_id')->nullable()->after('id')->constrained()->restrictOnDelete();
            $t->foreignId('branch_id')->nullable()->after('company_id')->constrained()->nullOnDelete();
            $t->boolean('is_active')->default(true)->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->dropConstrainedForeignId('branch_id');
            $t->dropConstrainedForeignId('company_id');
            $t->dropColumn('is_active');
        });
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('companies');
    }
};
