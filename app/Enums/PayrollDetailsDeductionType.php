<?php

namespace App\Enums;

enum PayrollDetailsDeductionType: string
{
    // 'Cash Advance','Loan','Other Deduction','Others'
    case CASHADVANCE = "Cash Advance";
    case LOAN = "Loan";
    case OTHERDEDUCTION = "Other Deduction";
}
