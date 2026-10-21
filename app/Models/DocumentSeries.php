<?php

namespace App\Models;

use App\Enums\DocType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentSeries extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'doc_type',
        'series',
        'current_number',
        'is_active',
    ];

    protected $casts = [
        'doc_type' => DocType::class,
        'current_number' => 'integer',
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
