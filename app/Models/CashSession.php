<?php

namespace AppModels;

use AppEnumsCashSessionStatus;
use IlluminateDatabaseEloquentFactoriesHasFactory;
use IlluminateDatabaseEloquentModel;
use IlluminateDatabaseEloquentRelationsBelongsTo;
use IlluminateDatabaseEloquentRelationsHasMany;

class CashSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_register_id',
        'opened_by',
        'closed_by',
        'status',
        'open_flag',
        'opening_amount',
        'expected_cash',
        'actual_cash',
        'difference',
        'notes',
        'opened_at',
        'closed_at',
    ];

    protected $casts = [
        'status' => CashSessionStatus::class,
        'open_flag' => 'boolean',
        'opening_amount' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'actual_cash' => 'decimal:2',
        'difference' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function register(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class, 'cash_register_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(CashMovement::class);
    }

    public function counts(): HasMany
    {
        return $this->hasMany(CashSessionCount::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
