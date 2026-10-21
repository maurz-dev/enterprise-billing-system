<?php

namespace App\Enums;

enum PaymentCondition: string
{
    case CASH = 'cash';
    case CREDIT = 'credit';
}
