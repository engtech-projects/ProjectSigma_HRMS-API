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
        $dtrSchedule = $employee->dtrSchedule($date);
        $dtrEvents = $this->dtrEvents($date);
        $dtrAttendance = $this->dtrAttendance($employee, $date);
        $attendanceMetadata = $this->getWorkingInterval($dtrAttendance);
        $dtrOvertime = $this->dtrOvertime($employee, $date);
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
            "leave" => $employee->dtrLeave($employee, $date),
            "events" => $this->dtrEvents($date),

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

    public function dtrSchedule($employee, $date)
    {
        $schedule = $employee->employee_schedule()->where('startRecur', $date)->get();
        return $schedule;
    }
    public function dtrAttendance($employee, $date)
    {
        $attendance = $employee->attendance_log()->where('date', $date)->get();
        return $attendance;
    }
    public function dtrOvertime($employee, $date)
    {
        $overtime = $employee->employee_overtime()->where('overtime_date', $date)->approved()->get();
        return $overtime;
    }

    public function dtrEvents($date)
    {
        return Events::whereDate('start_date', '<=', $date)->whereDate('end_date', '>=', $date)->get();
    }
}
