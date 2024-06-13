<?php

namespace App\Enums;

enum AttendanceSettings: string
{
    case LATE_ALLOWANCE = 'Late allowance min';
    case LATE_ABSENT = 'Late halfday min';
}
