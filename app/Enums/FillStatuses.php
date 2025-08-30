<?php

namespace App\Enums;

use App\Enums\Traits\EnumHelper;

enum FillStatuses: string
{
    use EnumHelper;
    case PENDING = "Pending";
    case OPEN = 'Open';
    case FILLED = "Filled";
    case CANCELLED = "Cancelled";
    case HOLD = "Hold";
}
