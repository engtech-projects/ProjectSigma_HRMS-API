<?php

namespace App\Enums;

use App\Enums\Traits\EnumHelper;
use App\Models\EmployeeLeaves;
use App\Models\TravelOrder;
use App\Models\Overtime;
use App\Models\AllowanceRequest;

enum VoidRequestModels: string
{
    use EnumHelper;
    case RequestLeaves = EmployeeLeaves::class;
    case RequestTravelOrder = TravelOrder::class;
    case RequestOvertime = Overtime::class;
    case RequestAllowance = AllowanceRequest::class;
}
