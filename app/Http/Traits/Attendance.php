<?php

namespace App\Http\Traits;

use App\Enums\AttendanceLogType;
use App\Helpers;
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

    public function getRegOvertime($data)
    {

        $regOT = 0;
        $regHolidayOT = 0;
        $specHolidayOT = 0;
        foreach ($data["schedule"] as $schedule) {
            $scheduleDate = Carbon::parse($schedule["startRecur"]);

            foreach ($data["overtime"] as $otValue) {
                $overtimeDate = Carbon::parse($otValue["overtime_date"]);
                if ($scheduleDate->isSameDay($overtimeDate)) {
                    $startTime = Carbon::parse($otValue["overtime_start_time"]);
                    $endTime = Carbon::parse($otValue["overtime_end_time"]);
                    if ($this->scheduleHaveTravelOrder($data["travel_orders"], $overtimeDate)) {
                        if ($this->scheduleHaveEvent($data["events"], $overtimeDate)) {
                            $regHolidayOT += $startTime->diffInHours($endTime);
                        } else {
                            $regOT += $startTime->diffInHours($endTime);
                        }
                    }
                }
            }
        }
        return [
            "reg_OT" => $regOT,
            "reg_holiday_OT" => $specHolidayOT,
            "spec_holiday_OT" => $specHolidayOT
        ];
    }

    public function scheduleHaveTravelOrder($travelOrders, $date)
    {
        foreach ($travelOrders as $travel) {
            $traveDate = Carbon::parse($travel["date_and_time_of_travel"]);
            if ($traveDate->isSameDay($date)) {
                return true;
            }
            continue;
        }
        return false;
    }
    public function scheduleHaveEvent($events, $date)
    {
        foreach ($events as $event) {
            $eventRange = Helpers::dateRange([
                "period_start" => $event["start_date"],
                "period_end" => $event["end_date"]
            ]);
            foreach ($eventRange as $eventDate) {
                $eventDate = Carbon::parse($eventDate["date"]);
                if ($eventDate->isSameDay($date)) {
                    return true;
                }
                continue;
            }
        }
        return false;
    }

    public function calculateOvertime($overtime, $date)
    {
        $duration = 0;
        foreach ($overtime as $otValue) {
            $overtimeDate = Carbon::parse($otValue["overtime_date"]);
            if ($date->isSameDay($overtimeDate)) {
                $startTime = Carbon::parse($otValue["overtime_start_time"]);
                $endTime = Carbon::parse($otValue["overtime_end_time"]);
                $duration += $startTime->diffInHours($endTime);
            }
        }
        return $duration;
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
