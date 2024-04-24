<?php

namespace App\Http\Traits;

use App\Models\Employee;
use App\Models\Events;

class CalculateAttendance
{
    public static function totalOfOvertime(Employee $employee)
    {
        return $employee;
    }
    public static function totalOfRestDay(Employee $employee)
    {
        $events = Events::all();
        return $events;
    }
    public function totalOfRegularWorkingDay(Employee $employee)
    {
    }
    public function totalOfSpecialHoliday(Employee $employee)
    {
    }

    public function totalOfRegularHoliday(Employee $employee)
    {
    }
}
