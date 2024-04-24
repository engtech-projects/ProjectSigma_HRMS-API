<?php

namespace App\Http\Requests;

enum PayrollType: string
{
    case WEEKLY = "weekly";
    case BI_MONTHLY = "bi-monthly";
    case MONTHLY = "monthly";
}
