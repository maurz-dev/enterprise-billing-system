<?php

namespace App\Enums;

enum PurchaseStatus: string
{
    case DRAFT = 'draft';
    case RECEIVED = 'received';
    case VOIDED = 'voided';
}
