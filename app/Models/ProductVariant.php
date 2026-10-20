<?php

namespace AppModels;

use IlluminateDatabaseEloquentFactoriesHasFactory;
use IlluminateDatabaseEloquentModel;
use IlluminateDatabaseEloquentRelationsBelongsTo;
use IlluminateDatabaseEloquentRelationsHasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'product_id',
        'sku',
        'barcode',
        'name',
        'attributes',
        'purchase_price',
        'sale_price',
        'min_stock',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'attributes' => 'array',
        'purchase_price' => 'decimal:4',
        'sale_price' => 'decimal:4',
        'min_stock' => 'decimal:4',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
