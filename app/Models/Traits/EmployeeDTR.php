<?php

namespace App\Models\Traits;

use App\Models\Events;

trait EmployeeDTR
{
    public function travel_order_dtr($date)
    {

        return $this->employee_travel_order()->whereDate('date_and_time_of_travel', $date)->get();
    }
    public function schedule_dtr($employee, $date)
    {
        $schedule = $employee->employee_schedule()
            ->where('startRecur', $date)
            ->get();
        return $schedule;
    }
    public function attendance_dtr($date)
    {
        return $this->attendance_log()->where('date', $date)->get();
    }
    public function overtime_dtr($employee, $date)
    {
        $overtime = $employee->employee_overtime()
            ->get();
        return $overtime;
    }

    public function events_dtr($date)
    {
        return Events::whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->get();
    }
    public function leave_dtr($date)
    {
        return $this->employee_leave()
            ->whereDate('date_of_absence_from', '<=', $date)
            ->whereDate('date_of_absence_to', '>=', $date)
            ->get();
    }
}
