<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $t->string('name', 150);
            $t->string('code', 30)->nullable();
            $t->text('description')->nullable();
            $t->decimal('hourly_rate', 10, 2)->default(50.00);
            $t->decimal('budget', 14, 2)->nullable();
            $t->string('status', 20)->default('active'); // active|completed|archived
            $t->timestamps();

            $t->index(['company_id', 'status']);
        });

        Schema::create('tasks', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $t->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $t->string('title', 255);
            $t->text('description')->nullable();
            $t->string('status', 20)->default('logged'); // logged|running|completed|invoiced
            $t->boolean('is_billable')->default(true);
            $t->decimal('hourly_rate', 10, 2)->default(50.00);
            $t->unsignedInteger('total_seconds')->default(0);
            $t->decimal('hours', 8, 2)->default(0.00);
            $t->date('due_date')->nullable();
            $t->timestamps();

            $t->index(['company_id', 'status']);
            $t->index('project_id');
            $t->index('contact_id');
        });

        Schema::create('task_timers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('started_at');
            $t->timestamp('ended_at')->nullable();
            $t->unsignedInteger('duration_seconds')->default(0);
            $t->text('notes')->nullable();
            $t->timestamps();

            $t->index(['task_id', 'ended_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_timers');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('projects');
    }
};
