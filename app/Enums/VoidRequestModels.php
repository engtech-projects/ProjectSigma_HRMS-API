<?php

namespace App\Enums;

use App\Enums\Traits\EnumHelper;
use App\Models\EmployeeLeaves;

enum VoidRequestModels: string
{
    use EnumHelper;
    case RequestLeaves = EmployeeLeaves::class;
}
