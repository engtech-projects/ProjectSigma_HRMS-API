<?php

namespace App\Interfaces;

interface Attendance
{
    public function getTotalOfWorkingHours($employee);
    public function getTotalOfOvertime($employee);
    public function getTotalOfRegularHoliday($employee);
    public function getTotalOfSpecialHoliday($employee);
    public function getTotalOfRestDay($employee);
}
