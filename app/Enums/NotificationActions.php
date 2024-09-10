<?php

namespace App\Enums;

use App\Models\AllowanceRequest;
use App\Models\CashAdvance;
use App\Models\EmployeeLeaves;
use App\Models\EmployeePanRequest;
use App\Models\FailureToLog;
use App\Models\ManpowerRequest;
use App\Models\Overtime;
use App\Models\PayrollRecord;
use App\Models\TravelOrder;

enum NotificationActions: string
{
    case VIEW = "View";
    case APPROVE = "Approve";

    public static function toArray(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->name] = $case->value;
        }
        return $array;
    }

    public static function toArraySwapped(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->value] = $case->name;
        }
        return $array;
    }
}
