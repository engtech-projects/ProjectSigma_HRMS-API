<?php

namespace App\Enums;

enum PayrollDetailsDeductionType: string
{
    case CASHADVANCE = "Cash Advance";
    case LOAN = "Loan";
    case OTHERDEDUCTION = "Other Deduction";
}
