<?php

namespace App\Enums;

use App\Enums\Traits\EnumHelper;

enum RequestStatuses: string
{
    use EnumHelper;

    case PENDING = "Pending";
    case APPROVED = 'Approved';
    case DENIED = "Denied";
    case VOID = "Voided";

}
