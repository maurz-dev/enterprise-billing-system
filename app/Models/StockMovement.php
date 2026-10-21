<?php

namespace App\Models;

use App\Enums\StockMovementDirection;
use App\Enums\StockMovementReason;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'warehouse_id',
        'product_variant_id',
        'direction',
        'reason',
        'quantity',
        'unit_cost',
        'total_cost',
        'balance_quantity',
        'balance_avg_cost',
        'reference_type',
        'reference_id',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'direction' => StockMovementDirection::class,
        'reason' => StockMovementReason::class,
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:6',
        'total_cost' => 'decimal:6',
        'balance_quantity' => 'decimal:4',
        'balance_avg_cost' => 'decimal:6',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
