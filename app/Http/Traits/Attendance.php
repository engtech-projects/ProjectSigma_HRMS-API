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

    public function getMetaData(Collection $data)
    {
        $overtime = $this->calculateOvertime($data);
        $regular = $this->calculateAttendance($data);
        return [
            "overtime" => $overtime,
            "regular" => $regular,
        ];
    }
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
        return $duration;
    }
    public function calculateAttendance($data)
    {
        $regHRS = 0;
        $regHolidayHRS = 0;
        $specHolidayHRS = 0;
        $restDayHRS = 0;
        $duration = 0;
        $lastTimeIn = null;
        foreach ($data["attendance"] as $attendance) {
            $time = Carbon::parse($attendance->time);
            if ($attendance["log_type"] == AttendanceLogType::TIME_IN->value) {
                $lastTimeIn = $time;
            } else {
                if ($lastTimeIn !== null) {
                    $lastTimeInDuration = $lastTimeIn->diffInHours($time);
                    $duration += $lastTimeInDuration;
                    $lastTimeIn = null;
                }
            }
            $attendanceDate = Carbon::parse($attendance->date);
            if ($this->hasSchedule($data["schedule"], $attendanceDate)) {
                if ($this->scheduleHaveTravelOrder($data["travel_orders"], $attendance->date)) {

                    $regHolidayHRS = $duration;
                } else {
                    $regHRS = $duration;
                }
            } else {
                $restDayHRS = $duration;
            }
        }


        return [
            "reg_hrs" => $regHRS,
            "reg_holiday_hrs" => $regHolidayHRS,
            "spec_holiday_hrs" => $specHolidayHRS,
            "rest_day_hrs" => $restDayHRS
        ];
    }
    public function getWorkingHours($attendance)
    {

        $duration = 0;
        $lastTimeIn = null;
        $time = Carbon::parse($attendance["time"]);

        if ($attendance["log_type"] == AttendanceLogType::TIME_IN->value) {
            $lastTimeIn = $time;
        } else {
            if ($lastTimeIn !== null) {
                $lastTimeInDuration = $lastTimeIn->diffInHours($time);
                $duration = $lastTimeInDuration;
                /*                 if ($time->diffIndays($lastTimeIn) > 0) {
                    $lateDuration = $time->diffInSeconds($time->copy()->startOfDay());
                    $totalLateDuration += $lateDuration;
                } */
                $lastTimeIn = null;
            }
        }
        $workingInterval = CarbonInterval::seconds($duration);

        return $workingInterval->totalHours;
    }
    public function calculateOvertime($data)
    {
        $regOT = 0;
        $regHolidayOT = 0;
        $specHolidayOT = 0;
        $restDayOT = 0;

        foreach ($data["overtime"] as $otValue) {
            $overtimeDate = Carbon::parse($otValue["overtime_date"]);
            $startTime = Carbon::parse($otValue["overtime_start_time"]);
            $endTime = Carbon::parse($otValue["overtime_end_time"]);
            if ($this->hasSchedule($data["schedule"], $overtimeDate)) {
                if ($this->scheduleHaveTravelOrder($data["travel_orders"], $overtimeDate)) {
                    if ($this->scheduleHaveEvent($data["events"], $overtimeDate)) {
                        $regHolidayOT += $startTime->diffInHours($endTime);
                    } else {
                        $regOT += $startTime->diffInHours($endTime);
                    }
                }
            } else {
                $restDayOT += $startTime->diffInHours($endTime);
            }
        }

        /*         if (count($data["schedule"]) > 0) {
            foreach ($data["schedule"] as $schedule) {
                $scheduleDate = Carbon::parse($schedule["startRecur"]);
                foreach ($data["overtime"] as $otValue) {
                    $overtimeDate = Carbon::parse($otValue["overtime_date"]);
                    $startTime = Carbon::parse($otValue["overtime_start_time"]);
                    $endTime = Carbon::parse($otValue["overtime_end_time"]);
                    if ($scheduleDate->isSameDay($overtimeDate)) {
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
        } else {
        } */

        return [
            "reg_OT" => $regOT,
            "reg_holiday_OT" => $specHolidayOT,
            "spec_holiday_OT" => $specHolidayOT,
            "rest_day_OT" => $restDayOT,
        ];
    }

    public function hasSchedule($schedules, $date)
    {
        foreach ($schedules as $schedule) {
            $scheduleDate = Carbon::parse($schedule["startRecur"]);
            if ($scheduleDate->isSameDay($date)) {
                return true;
            }
            continue;
        }
        return false;
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
