<?php

namespace App\Enums;

enum AttendanceLogType: string
{
    case TIME_IN = "In";
    case TIME_OUT = "Out";
}
