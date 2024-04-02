<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

final class LeaveRequestType extends Enum
{
    const SICK_CHECKUP = 'Sick/Checkup';
    const SPECIAL = "Special";
    const CELEBRATION = "Celebration";
    const VACATION = "Vacation";
    const MANDATORY = "Mandatory";
    const LEAVE = "Leave";
    const BEREAVEMENT = "Bereavement";
    const MATERNITY_PATERNITY = "Maternity/Paternity";
    const OTHER = "Other";
}
