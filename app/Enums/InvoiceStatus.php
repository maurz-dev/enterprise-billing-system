<?php

namespace AppEnums;

enum InvoiceStatus: string
{
    case DRAFT = 'draft';
    case ISSUED = 'issued';
    case PARTIALLY_PAID = 'partially_paid';
    case PAID = 'paid';
    case VOIDED = 'voided';

    public function isModifiable(): bool
    {
        return $this === self::DRAFT;
    }

    public function isClosed(): bool
    {
        return in_array($this, [self::PAID, self::VOIDED]);
    }
}
