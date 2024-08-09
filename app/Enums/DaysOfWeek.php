<?php

namespace App\Enums;

enum DaysOfWeek: string
{
    case SUNDAY = "Sunday";
    case MONDAY = 'Monday';
    case TUESDAY = "Tuesday";
    case WEDNESDAY = "Wednesday";
    case THURSDAY = "Thursday";
    case FRIDAY = "Friday";
    case SATURDAY = "Saturday";

    case SUNDAY_INDEX = "0";
    case MONDAY_INDEX = '1';
    case TUESDAY_INDEX = "2";
    case WEDNESDAY_INDEX = "3";
    case THURSDAY_INDEX = "4";
    case FRIDAY_INDEX = "5";
    case SATURDAY_INDEX = "6";
}
