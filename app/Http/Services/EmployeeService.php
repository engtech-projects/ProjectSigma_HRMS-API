<?php

namespace App\Http\Services;

use App\Helpers;
use App\Http\Traits\Attendance;
use App\Models\Events;
use Illuminate\Support\Carbon;

class EmployeeService
{
    use Attendance;
    public function employeeDTR($employee, $date)
    {
        $date = Carbon::parse($date);
        $dtrSchedule = $employee->dtrSchedule($employee, $date);
        $dtrEvents = $employee->dtrEvents($date);
        $dtrAttendance = $employee->dtrAttendance($employee, $date);
        $attendanceMetadata = $this->getWorkingInterval($dtrAttendance);
        $dtrOvertime = $employee->dtrOvertime($employee, $date);
        $overtimeMetadata = $this->getRegOvertime([
            "schedule" => $dtrSchedule,
            "events" => $dtrEvents,
            "attendance" => $dtrAttendance,
            "overtime" => $dtrOvertime
        ]);
        return [
            "schedule" => $dtrSchedule,
            "attendance" => $dtrAttendance,
            "ovetime" => $dtrOvertime,
            "leave" => $employee->dtrLeave($date),
            "events" => $dtrEvents,
            "metadata" => [
                "regular_hrs" => $attendanceMetadata->totalHours,
                "regular_holiday_hrs" => 0,
                "regular_overtime_hrs" => $overtimeMetadata["reg_OT"],
                "spec_holiday_overtime_hrs" => $overtimeMetadata["reg_holiday_OT"],
                "rest_overtime_hrs" => 0,
                "reg_holiday_overtime" => 0,

            ]
        ];
    }
}
