<?php

namespace App\Enums\Reports;

use App\Enums\Traits\EnumHelper;

enum OtherDeductionReports: string
{
    use EnumHelper;
    case MP2 = "MP2";
}
