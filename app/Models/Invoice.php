<?php

namespace AppModels;

use AppEnumsDocType;
use AppEnumsInvoiceStatus;
use AppEnumsPaymentCondition;
use IlluminateDatabaseEloquentBuilder;
use IlluminateDatabaseEloquentFactoriesHasFactory;
use IlluminateDatabaseEloquentModel;
use IlluminateDatabaseEloquentRelationsBelongsTo;
use IlluminateDatabaseEloquentRelationsHasMany;
use IlluminateSupportStr;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'company_id',
        'branch_id',
        'warehouse_id',
        'contact_id',
        'user_id',
        'cash_session_id',
        'quote_id',
        'related_invoice_id',
        'doc_type',
        'series',
        'number',
        'status',
        'payment_condition',
        'issue_date',
        'due_date',
        'currency',
        'exchange_rate',
        'subtotal',
        'discount_total',
        'tax_total',
        'total',
        'paid_total',
        'sunat_status',
        'hash',
        'xml_path',
        'cdr_path',
        'notes',
        'issued_at',
        'voided_at',
        'void_reason',
    ];

    protected $casts = [
        'doc_type' => DocType::class,
        'status' => InvoiceStatus::class,
        'payment_condition' => PaymentCondition::class,
        'issue_date' => 'date',
        'due_date' => 'date',
        'exchange_rate' => 'decimal:6',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_total' => 'decimal:2',
        'issued_at' => 'datetime',
        'voided_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->uuid)) {
                $invoice->uuid = (string) Str::uuid();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function cashSession(): BelongsTo
    {
        return $this->belongsTo(CashSession::class);
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function relatedInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'related_invoice_id');
    }

    public function creditDebitNotes(): HasMany
    {
        return $this->hasMany(Invoice::class, 'related_invoice_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function getPendingAmountAttribute(): float
    {
        return (float) max(0, $this->total - $this->paid_total);
    }

    public function getFullCorrelativeAttribute(): ?string
    {
        return $this->series && $this->number
            ? sprintf('%s-%08d', $this->series, $this->number)
            : null;
    }

    public function scopeIssued(Builder $query): Builder
    {
        return $query->whereIn('status', [InvoiceStatus::ISSUED, InvoiceStatus::PARTIALLY_PAID]);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::PAID);
    }
}
