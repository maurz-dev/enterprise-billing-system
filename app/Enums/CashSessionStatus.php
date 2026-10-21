<?php

namespace App\Enums;

enum CashSessionStatus: string
{
    case OPEN = 'open';
    case CLOSED = 'closed';
}
