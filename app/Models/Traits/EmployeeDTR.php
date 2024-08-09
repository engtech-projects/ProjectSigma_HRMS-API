<?php

namespace App\Models\Traits;

use App\Models\Events;

trait EmployeeDTR
{
    public function travel_order_dtr($date)
    {

        return $this->employee_travel_order()
            ->whereDate('date_of_travel', "<=", $date)
            ->get()
            ->filter(function ($travel) use ($date) {
                return $travel->date_time_end->gte($date);
            });
    }
    public function schedule_dtr($date)
    {
        $schedule = $this->employee_schedule()
            ->whereDate('startRecur', $date)->get();
        return $schedule;
    }
    public function attendance_dtr($date)
    {
        return $this->attendance_log()->where('date', $date)->get();
    }
    public function overtime_dtr($date)
    {
        return $this->employee_overtime()
            ->whereDate('overtime_date', $date)->get();
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
