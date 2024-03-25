<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

enum AttendanceLogType: string
{
    case TIME_IN = "In";
    case TIME_OUT = "Out";
}
