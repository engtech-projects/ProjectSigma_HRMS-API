<?php

namespace App\Enums;

enum LoanPaymentPostingStatusType: string
{
    case POSTED = "Posted";
    case NOTPOSTED = "Not Posted";
}
