<?php

namespace App\Enums;

use App\Enums\Traits\EnumHelper;

enum RequestStatuses: string
{
    use EnumHelper;
    case APPROVED = "Approved";
    case PENDING = "Pending";
    case DENIED = "Denied";
    case VOID = "Voided";
}
