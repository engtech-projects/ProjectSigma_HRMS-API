<?php

namespace App\Enums;

enum PayrollType: string
{
    case WEEKLY = "weekly";
    case BI_MONTHLY = "bi-monthly";
    case MONTHLY = "monthly";
}
