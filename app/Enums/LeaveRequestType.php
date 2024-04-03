<?php

namespace App\Enums;

enum LeaveRequestType: string
{
    case SICK_CHECKUP = 'Sick/Checkup';
    case SPECIAL = "Special";
    case CELEBRATION = "Celebration";
    case VACATION = "Vacation";
    case MANDATORY = "Mandatory";
    case LEAVE = "Leave";
    case BEREAVEMENT = "Bereavement";
    case MATERNITY_PATERNITY = "Maternity/Paternity";
    case OTHER = "Other";
}
