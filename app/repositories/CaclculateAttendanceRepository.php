<?php

namespace App\repositories;

use App\Interfaces\Attendance;
use App\Models\Events;

class CalculateAttendanceRepository implements Attendance
{
    public function getTotalOfOvertime($employee)
    {
    }
    public function getTotalOfRegularHoliday($employee)
    {
    }

    public function getTotalOfRestDay($employee)
    {
        $events = Events::all();

        return $events;
    }

    public function getTotalOfWorkingHours($employee)
    {
    }

    public function getTotalOfSpecialHoliday($employee)
    {
    }
}
