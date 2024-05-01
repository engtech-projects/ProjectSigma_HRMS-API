<?php

namespace App\Http\Services;

use App\Http\Traits\Attendance;
use App\Models\Events;
use Illuminate\Support\Carbon;

class EmployeeService
{
    use Attendance;
    public function employeeDTR($employee, $date)
    {
        $date = Carbon::parse($date);
        $dtrAttendance = $this->dtrAttendance($employee, $date);
        $attendanceMetadata = $this->getWorkingInterval($dtrAttendance);
        return [
            "schedule" => $this->dtrSchedule($employee, $date),
            "attendance" => $dtrAttendance,
            "ovetime" => $this->dtrOvertime($employee, $date),
            "leave" => $this->dtrLeave($employee, $date),
            "events" => $this->dtrEvents($date),
            "metadata" => [
                "regular_hrs" => $attendanceMetadata->totalHours,
                "regular_holidays_hrs" => 0,
                "regular_overtime_hrs" => 0,
                "rest_overtime_hrs" => 0,
                "reg_holiday_overtime" => 0,
                "spec_holiday_overtime_hrs" => 0

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
        $overtime = $employee->employee_has_overtime()->where('overtime_date', $date)->approved()->get();
        return $overtime;
    }
    public function dtrLeave($employee, $date)
    {
        $leave = $employee->employee_leave('leave_date', $date)->withPayLeave()->approved()->get();
        return $leave;
    }
    public function dtrEvents($date)
    {
        return Events::whereDate('start_date', $date)->get();
    }
    public function dtrMetadata($attendances)
    {
        return $attendances;
    }
}
