<?php

namespace App\Enums;

enum SalaryRequestType: string
{
    case SALARY_TYPE_FIXED_RATE = "Fixed Rate";
    case SALARY_TYPE_NON_FIXED = "Non Fixed Rate";
    case SALARY_TYPE_WEEKLY = "Weekly";
    case SALARY_TYPE_MONTHLY = "Monthly";
}
