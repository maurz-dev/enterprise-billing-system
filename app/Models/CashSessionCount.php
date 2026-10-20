<?php

namespace AppModels;

use IlluminateDatabaseEloquentFactoriesHasFactory;
use IlluminateDatabaseEloquentModel;
use IlluminateDatabaseEloquentRelationsBelongsTo;

class CashSessionCount extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_session_id',
        'denomination',
        'quantity',
        'total',
    ];

    protected $casts = [
        'denomination' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(CashSession::class, 'cash_session_id');
    }
}
