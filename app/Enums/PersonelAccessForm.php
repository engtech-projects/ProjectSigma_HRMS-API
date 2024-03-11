<?php

namespace App\Enums;

enum PersonalAccessForm : string
{
    //employee type
    case SALARY_TYPE_FIXED_RATE = "newhire";
    case SALARY_TYPE_NON_FIXED = "termination";
    case SALARY_TYPE_WEEKLY = "transfer";
    case SALARY_TYPE_MONTHLY = "promotion";
    //salary office work
    case SALARY_TYPE_OFFICE_TYPE_PMS = "promotion";
    case SALARY_TYPE_OFFICE_TYPE_OFFICE = "Office";
    case SALARY_TYPE_OFFICE_TYPE_PROJECT_CODE = "Project Code";

    //salary
    case SALARY_TYPE_FIXED_RATE = "Fixed Rate";
    case SALARY_TYPE_NON_FIXED = "Non Fixed Rate";
    case SALARY_TYPE_WEEKLY = "Weekly";
    case SALARY_TYPE_MONTHLY = "Monthly";
    //termination
    case TERMINATION_TYPE_VOLUNTARY = "Voluntary";
    case TERMINATION_TYPE_INVOLUNTARY = "Involuntary";
    case TERMINATION_ELIGIBLE_FOR_REHIRE_YES = "Yes";
    case TERMINATION_ELIGIBLE_FOR_REHIRE_NO = "No";
    case REASONS_TERMINATION_TYPE_VIOLATION = "Violation";
    case REASONS_TERMINATION_TYPE_VIOLATION = "Sanctions";
    case REASONS_TERMINATION_TYPE_VIOLATION = "Force Resign";
}
