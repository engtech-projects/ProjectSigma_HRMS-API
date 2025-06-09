<?php

namespace App\Enums;

use App\Enums\Traits\EnumHelper;
use App\Models\EmployeeLeaves;
use App\Models\TravelOrder;
use App\Models\Overtime;

enum VoidRequestModels: string
{
    use EnumHelper;
    case RequestLeaves = EmployeeLeaves::class;
    case RequestTravelOrder = TravelOrder::class;
    case RequestOvertime = Overtime::class;
}
