<?php

namespace App\Http\Traits;

use App\Enums\AttendanceLogType;
use App\Models\Employee;
use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

trait Attendance
{
    public function getWorkingInterval($attendances)
    {
        $duration = 0;
        $totalLateDuration = 0;
        $lastTimeIn = null;
        foreach ($attendances as $attendance) {
            $time = Carbon::parse($attendance["time"]);
            if ($attendance["log_type"] === AttendanceLogType::TIME_IN->value) {
                $lastTimeIn = $time;
            } else {
                if ($lastTimeIn !== null) {
                    $lastTimeInDuration = $lastTimeIn->diffInSeconds($time);
                    $duration += $lastTimeInDuration;
                    if ($time->diffIndays($lastTimeIn) > 0) {
                        $lateDuration = $time->diffInSeconds($time->copy()->startOfDay());
                        $totalLateDuration += $lateDuration;
                    }
                    $lastTimeIn = null;
                }
            }
        }
        $workingInterval = CarbonInterval::seconds($duration);
        return $workingInterval;
    }
    public function getRegHoliday()
    {
    }

    public function getRegOvertime()
    {
    }


    public function getRegHolidayOT()
    {
    }

    public function getRestOT()
    {
    }

    public function getSpecHolidayOT()
    {
    }
}
