<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

final class LeaveRequestType extends Enum
{
    public const SICK_CHECKUP = 'Sick/Checkup';
    public const SPECIAL = "Special";
    public const CELEBRATION = "Celebration";
    public const VACATION = "Vacation";
    public const MANDATORY = "Mandatory";
    public const LEAVE = "Leave";
    public const BEREAVEMENT = "Bereavement";
    public const MATERNITY_PATERNITY = "Maternity/Paternity";
    public const OTHER = "Other";
}
