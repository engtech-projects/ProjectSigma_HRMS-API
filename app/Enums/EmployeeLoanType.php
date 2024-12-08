<?php

namespace App\Enums;

enum EmployeeLoanType: string
{
    case HDMF_MPL = "HDMF MPL";
    case COOP = "COOP LOAN";
    case SSS = "SSS LOAN";
    case MP2 = "MP2";
    case CALAMITY_LOAN = "CALAMITY LOAN";
    case OTHER_DEDUCTIONS = "OTHER DEDUCTIONS";
}
