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
        $schedules = $employee->schedule_dtr($employee, $date);
        $events = $employee->events_dtr($date);
        $attendances = $employee->attendance_dtr($employee, $date);
        $travelOrders = $employee->travel_order_dtr($date);
        $attendanceMetadata = $this->getWorkingInterval($attendances);
        $overtime = $employee->overtime_dtr($employee, $date);
        $overtimeMetadata = $this->getRegOvertime([
            "schedule" => $schedules,
            "events" => $events,
            "attendance" => $attendances,
            "overtime" => $overtime,
            "travel_orders" => $travelOrders
        ]);
        return [
            "schedule" => $schedules,
            "attendance" => $attendances,
            "travel_order" => $travelOrders,
            "ovetime" => $overtime,
            "leave" => $employee->leave_dtr($date),
            "events" => $events,
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
