<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskTimer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TaskWebController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $company = Company::first();

        $tasks = Task::with(['project', 'client', 'activeTimer'])
            ->orderByDesc('id')
            ->get();

        $activeTimer = TaskTimer::with('task')
            ->whereNull('ended_at')
            ->latest()
            ->first();

        $recentTasks = Task::with(['project', 'client'])
            ->orderByDesc('updated_at')
            ->take(8)
            ->get();

        $projects = Project::where('status', 'active')->get();
        $clients = Contact::where('is_customer', true)->get();

        return response()->json([
            'status' => 'ok',
            'tasks' => $tasks,
            'active_timer' => $activeTimer ? [
                'id' => $activeTimer->id,
                'task_id' => $activeTimer->task_id,
                'task_title' => $activeTimer->task->title,
                'started_at' => $activeTimer->started_at->toIso8601String(),
                'elapsed_seconds' => Carbon::now()->diffInSeconds($activeTimer->started_at),
            ] : null,
            'recent' => $recentTasks,
            'projects' => $projects,
            'clients' => $clients,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'hourly_rate' => 'nullable|numeric|min:0',
            'hours' => 'nullable|numeric|min:0',
            'start_timer_now' => 'nullable|boolean',
        ]);

        $company = Company::first();

        $task = Task::create([
            'company_id' => $company->id,
            'project_id' => $request->project_id,
            'contact_id' => $request->contact_id,
            'user_id' => 1,
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->start_timer_now ? 'running' : 'logged',
            'is_billable' => true,
            'hourly_rate' => $request->hourly_rate ?? 65.00,
            'hours' => $request->hours ?? 0.00,
            'total_seconds' => $request->hours ? (int) ($request->hours * 3600) : 0,
            'due_date' => $request->due_date ?? Carbon::today(),
        ]);

        if ($request->start_timer_now) {
            // Stop any other active timers first
            TaskTimer::whereNull('ended_at')->each(function ($timer) {
                $timer->update([
                    'ended_at' => Carbon::now(),
                    'duration_seconds' => Carbon::now()->diffInSeconds($timer->started_at),
                ]);
            });

            TaskTimer::create([
                'task_id' => $task->id,
                'user_id' => 1,
                'started_at' => Carbon::now(),
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Tarea creada exitosamente.',
            'task' => $task->load(['project', 'client', 'activeTimer']),
        ], 201);
    }

    public function startTimer(Request $request, int $id): JsonResponse
    {
        $task = Task::findOrFail($id);

        // Stop any running timer first
        $runningTimers = TaskTimer::whereNull('ended_at')->get();
        foreach ($runningTimers as $t) {
            $diff = Carbon::now()->diffInSeconds($t->started_at);
            $t->update([
                'ended_at' => Carbon::now(),
                'duration_seconds' => $diff,
            ]);
            $t->task->increment('total_seconds', $diff);
            $t->task->update(['status' => 'completed']);
        }

        $newTimer = TaskTimer::create([
            'task_id' => $task->id,
            'user_id' => 1,
            'started_at' => Carbon::now(),
        ]);

        $task->update(['status' => 'running']);

        return response()->json([
            'status' => 'ok',
            'message' => 'Temporizador iniciado para: ' . $task->title,
            'timer' => [
                'id' => $newTimer->id,
                'task_id' => $task->id,
                'task_title' => $task->title,
                'started_at' => $newTimer->started_at->toIso8601String(),
                'elapsed_seconds' => 0,
            ],
            'task' => $task,
        ]);
    }

    public function stopTimer(Request $request, int $id): JsonResponse
    {
        $task = Task::findOrFail($id);
        $timer = TaskTimer::where('task_id', $task->id)->whereNull('ended_at')->latest()->first();

        $elapsed = 0;
        if ($timer) {
            $elapsed = Carbon::now()->diffInSeconds($timer->started_at);
            $timer->update([
                'ended_at' => Carbon::now(),
                'duration_seconds' => $elapsed,
            ]);
            $task->increment('total_seconds', $elapsed);
        }

        $task->update([
            'status' => 'completed',
            'hours' => round($task->total_seconds / 3600, 2),
        ]);

        return response()->json([
            'status' => 'ok',
            'message' => 'Temporizador detenido.',
            'task' => $task->fresh(['project', 'client']),
            'elapsed_seconds' => $elapsed,
        ]);
    }

    public function convertToInvoiceUrl(Request $request): JsonResponse
    {
        $request->validate([
            'task_ids' => 'required|array|min:1',
            'task_ids.*' => 'exists:tasks,id',
        ]);

        $url = route('invoices.create', ['tasks' => implode(',', $request->task_ids)]);

        return response()->json([
            'status' => 'ok',
            'redirect_url' => $url,
        ]);
    }
}
