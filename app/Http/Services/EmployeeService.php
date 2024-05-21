<?php

namespace App\Http\Services;

use Illuminate\Support\Carbon;
use App\Http\Traits\Attendance;
use App\Models\AttendanceLog;

class EmployeeService
{
    use Attendance;
    public function employeeDTR($employee, $date)
    {
        $schedules_attendances = $employee->applied_schedule_with_attendance($date);
        $schedules = $employee->applied_schedule($date);
        $events = $employee->events_dtr($date);
        $attendances = $employee->daily_attendance_schedule($date);
        $travelOrders = $employee->travel_order_dtr($date);
        $overtime = $employee->employee_overtime()->where('overtime_date', $date)->get();

        $leave = $employee->leave_dtr($date);
        $collection = [
            "schedules_attendances" => $schedules_attendances,
            "schedule" => $schedules,
            "events" => $events,
            "attendance" => $attendances,
            "overtime" => $overtime,
            "travel_orders" => $travelOrders,
            "leave" => $leave
        ];
        return [
            "schedule" => $schedules,
            "attendance" => $attendances,
            "travel_order" => $travelOrders,
            "ovetime" => $overtime,
            "leave" => $employee->leave_dtr($date),
            "events" => $events,
            "metadata" => $this->getMetaData($collection, $date),
        ];
    }
}
