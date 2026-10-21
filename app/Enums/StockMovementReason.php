<?php

namespace App\Enums;

enum StockMovementReason: string
{
    case INITIAL = 'initial';
    case PURCHASE = 'purchase';
    case SALE = 'sale';
    case RETURN = 'return';
    case ADJUSTMENT = 'adjustment';
    case TRANSFER = 'transfer';
    case VOID = 'void';
}
