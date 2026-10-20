<?php

namespace AppEnums;

enum PurchaseStatus: string
{
    case DRAFT = 'draft';
    case RECEIVED = 'received';
    case VOIDED = 'voided';
}
