<?php

namespace App\Http\Services;

use Illuminate\Support\Carbon;
use App\Http\Traits\Attendance;

class EmployeeService
{
    use Attendance;
    public function employeeDTR($employee, $date)
    {
        $date = Carbon::parse($date);
        $schedules = $employee->applied_schedule($date);
        $events = $employee->events_dtr($date);
        $attendances = $employee->attendance_dtr($date);
        $travelOrders = $employee->travel_order_dtr($date);
        $overtime = $employee->overtime_dtr($date);
        $collection = [
            "schedule" => $schedules,
            "events" => $events,
            "attendance" => $attendances,
            "overtime" => $overtime,
            "travel_orders" => $travelOrders
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
