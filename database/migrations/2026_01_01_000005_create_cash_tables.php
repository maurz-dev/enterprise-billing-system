<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->restrictOnDelete();
            $t->string('code', 20);                     // CASH, YAPE, TRANSFER, CARD, STRIPE
            $t->string('name', 80);
            $t->string('type', 10);                     // cash | transfer | card | gateway | other
            $t->boolean('is_active')->default(true);
            $t->timestamps();

            $t->unique(['company_id', 'code']);
        });

        Schema::create('cash_registers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('branch_id')->constrained()->restrictOnDelete();
            $t->string('name', 80);
            $t->boolean('is_active')->default(true);
            $t->timestamps();

            $t->unique(['branch_id', 'name']);
        });

        // Turno de caja. `open_flag` = true solo mientras está abierto, NULL al cerrar:
        // el UNIQUE(cash_register_id, open_flag) impide dos turnos abiertos en la misma caja
        // y funciona igual en PostgreSQL y MySQL (los NULL no colisionan).
        Schema::create('cash_sessions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('cash_register_id')->constrained()->restrictOnDelete();
            $t->foreignId('opened_by')->constrained('users')->restrictOnDelete();
            $t->foreignId('closed_by')->nullable()->constrained('users')->restrictOnDelete();
            $t->string('status', 10)->default('open');  // open | closed
            $t->boolean('open_flag')->nullable()->default(true);
            $t->decimal('opening_amount', 14, 2)->default(0);
            $t->decimal('expected_cash', 14, 2)->nullable();
            $t->decimal('counted_cash', 14, 2)->nullable();
            $t->decimal('difference', 14, 2)->nullable();
            $t->text('notes')->nullable();
            $t->timestamp('opened_at')->useCurrent();
            $t->timestamp('closed_at')->nullable();
            $t->timestamps();

            $t->unique(['cash_register_id', 'open_flag'], 'uq_one_open_session_per_register');
            $t->index(['cash_register_id', 'opened_at']);
        });

        // Libro de caja: ingresos/egresos por método de pago.
        Schema::create('cash_movements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('cash_session_id')->constrained()->restrictOnDelete();
            $t->foreignId('payment_method_id')->constrained()->restrictOnDelete();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->string('type', 10);                     // income | expense
            $t->string('category', 30);                 // sale, refund, expense, withdrawal, deposit, other
            $t->decimal('amount', 14, 2);               // siempre positivo
            $t->string('description', 255)->nullable();
            $t->nullableMorphs('reference');
            $t->timestamp('occurred_at')->useCurrent();
            $t->timestamps();

            $t->index(['cash_session_id', 'payment_method_id']);
        });

        // Arqueo por método de pago al cierre (esperado vs contado).
        Schema::create('cash_session_counts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('cash_session_id')->constrained()->cascadeOnDelete();
            $t->foreignId('payment_method_id')->constrained()->restrictOnDelete();
            $t->decimal('expected_amount', 14, 2);
            $t->decimal('counted_amount', 14, 2);
            $t->decimal('difference', 14, 2);
            $t->timestamps();

            $t->unique(['cash_session_id', 'payment_method_id']);
        });

        DB::statement("ALTER TABLE cash_movements ADD CONSTRAINT chk_cash_mov CHECK (amount > 0 AND type IN ('income','expense'))");
        DB::statement("ALTER TABLE cash_sessions ADD CONSTRAINT chk_cash_status CHECK (status IN ('open','closed'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_session_counts');
        Schema::dropIfExists('cash_movements');
        Schema::dropIfExists('cash_sessions');
        Schema::dropIfExists('cash_registers');
        Schema::dropIfExists('payment_methods');
    }
};
