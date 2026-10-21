<?php

namespace App\Enums;

enum PaymentMethodType: string
{
    case CASH = 'cash';
    case TRANSFER = 'transfer';
    case CARD = 'card';
    case GATEWAY = 'gateway';
    case OTHER = 'other';
}
