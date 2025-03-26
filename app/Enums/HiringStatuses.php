<?php

namespace App\Enums;

use App\Enums\Traits\EnumHelper;

enum HiringStatuses: string
{
    use EnumHelper;

    case PROCESSING = "Processing";
    case FOR_HIRING = "For Hiring";
    case REJECTED = "Rejected";
    case HIRED = "Hired";

}
