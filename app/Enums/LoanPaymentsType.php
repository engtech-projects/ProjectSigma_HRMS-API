<?php

namespace App\Enums;

enum LoanPaymentsType: string
{
    case MANUAL = "Manual";
    case PAYROLL = "Payroll";
}
