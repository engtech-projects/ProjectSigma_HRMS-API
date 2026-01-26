<?php

namespace App\Enums;

enum EventTypes: string
{
    // ["Regular Holiday", "Special Holiday", "Company Event"]
    case REGULARHOLIDAY = "Regular Holiday";
    case SPECIALHOLIDAY = "Special Holiday";
    case COMPANYEVENT = "Company Event";
}
