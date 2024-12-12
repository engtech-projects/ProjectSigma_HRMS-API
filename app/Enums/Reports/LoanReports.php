<?php

namespace App\Enums\Reports;

use App\Enums\Traits\EnumHelper;

enum LoanReports: string
{
    use EnumHelper;
    case HDMF_MPL = "HDMF MPL";
    case COOP = "COOP LOAN";
    case SSS = "SSS LOAN";
    case CALAMITY_LOAN = "CALAMITY LOAN";
}
