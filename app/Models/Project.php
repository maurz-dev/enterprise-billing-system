<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'contact_id',
        'name',
        'code',
        'description',
        'hourly_rate',
        'budget',
        'status',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'budget' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
