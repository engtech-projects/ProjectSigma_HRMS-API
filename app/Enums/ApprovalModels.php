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
use App\Models\Request13thMonth;
use App\Models\RequestSalaryDisbursement;
use App\Models\RequestVoid;
use App\Models\TravelOrder;

enum ApprovalModels: string
{
    case ManpowerRequest = ManpowerRequest::class;
    case FailureToLog = FailureToLog::class;
    case EmployeePanRequest = EmployeePanRequest::class;
    case LeaveEmployeeRequest = EmployeeLeaves::class;
    case TravelOrder = TravelOrder::class;
    case CashAdvance = CashAdvance::class;
    case Overtime = Overtime::class;
    case GenerateAllowance = AllowanceRequest::class;
    case GeneratePayroll = PayrollRecord::class;
    case RequestSalaryDisbursement = RequestSalaryDisbursement::class;
    case Request13thMonth = Request13thMonth::class;
    case RequestVoid = RequestVoid::class;

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
