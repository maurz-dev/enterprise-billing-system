<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskTimer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        $client = Contact::where('is_customer', true)->first();
        $user = User::first() ?? User::create([
            'name' => 'David Bomba',
            'email' => 'david.bomba@enterprise.com',
            'password' => bcrypt('password123'),
        ]);

        $project = Project::create([
            'company_id' => $company->id,
            'contact_id' => $client?->id,
            'name' => 'Product Roadmap 2026',
            'code' => 'ROADMAP',
            'description' => 'Evolución de arquitectura SaaS y mejoras de autoservicio de facturación',
            'hourly_rate' => 65.00,
            'budget' => 5000.00,
            'status' => 'active',
        ]);

        // Task 1: Image 1 main task
        $t1 = Task::create([
            'company_id' => $company->id,
            'project_id' => $project->id,
            'contact_id' => $client?->id,
            'user_id' => $user->id,
            'title' => 'Self-serve billing upgrade',
            'description' => 'Desarrollo del flujo interactivo de autoservicio para emisión de facturas y notas de crédito en tiempo real.',
            'status' => 'running',
            'is_billable' => true,
            'hourly_rate' => 65.00,
            'total_seconds' => 5400, // 1h 30m
            'hours' => 1.50,
            'due_date' => Carbon::today(),
        ]);

        TaskTimer::create([
            'task_id' => $t1->id,
            'user_id' => $user->id,
            'started_at' => Carbon::now()->subMinutes(25),
            'ended_at' => null, // running right now
            'duration_seconds' => 1500,
            'notes' => 'Refactorización del servicio de facturación',
        ]);

        // Task 2: Image 1 recent task 0122
        $t2 = Task::create([
            'company_id' => $company->id,
            'project_id' => $project->id,
            'contact_id' => $client?->id,
            'user_id' => $user->id,
            'title' => 'Task #0122 - Configuración de correlativos SUNAT',
            'description' => 'Validación de bloqueo pesimista lockForUpdate en series F001 y B001.',
            'status' => 'completed',
            'is_billable' => true,
            'hourly_rate' => 65.00,
            'total_seconds' => 3600, // 1:00:00
            'hours' => 1.00,
            'due_date' => Carbon::today(),
        ]);

        // Task 3: Image 1 recent task Add CSV export
        $t3 = Task::create([
            'company_id' => $company->id,
            'project_id' => $project->id,
            'contact_id' => $client?->id,
            'user_id' => $user->id,
            'title' => 'Add CSV export for reports',
            'description' => 'Exportación de reportes de Kardex valorizado y ventas mensuales.',
            'status' => 'completed',
            'is_billable' => true,
            'hourly_rate' => 65.00,
            'total_seconds' => 93, // 0:01:33
            'hours' => 0.03,
            'due_date' => Carbon::today(),
        ]);

        // Task 4: Design new client dashboard
        $t4 = Task::create([
            'company_id' => $company->id,
            'project_id' => $project->id,
            'contact_id' => $client?->id,
            'user_id' => $user->id,
            'title' => 'Design new client dashboard',
            'description' => 'Prototipado de panel de control con métricas en tiempo real y estética Crater.',
            'status' => 'logged',
            'is_billable' => true,
            'hourly_rate' => 70.00,
            'total_seconds' => 9000, // 2:30:00
            'hours' => 2.50,
            'due_date' => Carbon::today()->addDays(2),
        ]);
    }
}
