<?php

namespace App\Enums;

enum TermsOfPaymentType: string
{
    case WEEKLY = "weekly";
    case MONTHLY = "monthly";
    case BIMONTHLY = "bimonthly";
}
