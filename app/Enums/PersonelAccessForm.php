<?php

namespace App\Enums;

enum PersonalAccessForm : string
{
    //employee type
    case SALARY_TYPE_FIXED_RATE = "newhire";
    case SALARY_TYPE_NON_FIXED = "termination";
    case SALARY_TYPE_WEEKLY = "transfer";
    case SALARY_TYPE_MONTHLY = "promotion";
    //salary
    case SALARY_TYPE_FIXED_RATE = "Fixed Rate";
    case SALARY_TYPE_NON_FIXED = "Non Fixed Rate";
    case SALARY_TYPE_WEEKLY = "Weekly";
    case SALARY_TYPE_MONTHLY = "Monthly";
}
