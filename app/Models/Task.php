<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'project_id',
        'contact_id',
        'user_id',
        'invoice_id',
        'title',
        'description',
        'status',
        'is_billable',
        'hourly_rate',
        'total_seconds',
        'hours',
        'due_date',
    ];

    protected $casts = [
        'is_billable' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'hours' => 'decimal:2',
        'total_seconds' => 'integer',
        'due_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function timers(): HasMany
    {
        return $this->hasMany(TaskTimer::class);
    }

    public function activeTimer()
    {
        return $this->hasOne(TaskTimer::class)->whereNull('ended_at');
    }

    public function getFormattedDurationAttribute(): string
    {
        $hours = floor($this->total_seconds / 3600);
        $minutes = floor(($this->total_seconds % 3600) / 60);
        $seconds = $this->total_seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    public function getBillableAmountAttribute(): float
    {
        $durationHours = $this->total_seconds > 0 ? ($this->total_seconds / 3600) : (float) $this->hours;
        return round($durationHours * (float) $this->hourly_rate, 2);
    }
}
