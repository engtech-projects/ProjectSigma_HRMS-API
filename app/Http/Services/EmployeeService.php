<?php

namespace App\Http\Services;

use Illuminate\Support\Carbon;
use App\Http\Traits\Attendance;

use App\Http\Payroll\Services\PayrollDeduction;

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
        $metaData = $this->getMetaData($collection, $date);
        return [
            "schedule" => $schedules,
            "attendance" => $attendances,
            "travel_order" => $travelOrders,
            "ovetime" => $overtime,
            "leave" => $employee->leave_dtr($date),
            "events" => $events,
            "meta_data" => $metaData,
            /*  "metadata" => [
                "regular_hrs" => $metaData["regular"]["reg_hrs"],
                "regular_holiday_hrs" => $metaData["regular"]["reg_holiday_hrs"],
                "special_holiday_hrs" => $metaData["regular"]["spec_holiday_hrs"],
                "rest_day_hrs" => $metaData["regular"]["rest_day_hrs"],
                "regular_overtime_hrs" => $metaData["overtime"]["reg_OT"],
                "spec_holiday_overtime_hrs" => $metaData["overtime"]["spec_holiday_OT"],
                "rest_overtime_hrs" => $metaData["overtime"]["rest_day_OT"],
                "reg_holiday_overtime" => $metaData["overtime"]["reg_holiday_OT"],

            ] */
        ];
    }
}
