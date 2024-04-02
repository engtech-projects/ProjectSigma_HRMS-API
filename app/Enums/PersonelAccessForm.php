<?php

namespace App\Enums;

enum PersonelAccessForm: string
{
    //request type
    case REQUEST_TYPE_NEW_HIRE = "newhire";
    case REQUEST_TYPE_TERMINATION = "termination";
    case REQUEST_TYPE_TRANSFER = "transfer";
    case REQUEST_TYPE_PROMOTION = "promotion";
    //salary office work
    case SALARY_TYPE_OFFICE_TYPE_PMS = "PMS";
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
    case REASONS_TERMINATION_TYPE_SANCTIONS = "Sanctions";
    case REASONS_TERMINATION_TYPE_FORCE_RESIGN = "Force Resign";

    public const REQUESTSTATUS_PENDING = "Pending";
    public const REQUESTSTATUS_APPROVED = "Approved";
    public const REQUESTSTATUS_DISAPPROVED = "Disapproved";
    public const REQUESTSTATUS_FILLED = "Filled";
    public const REQUESTSTATUS_HOLD = "Hold";
    public const REQUESTSTATUS_CANCELLED = "Cancelled";
}
