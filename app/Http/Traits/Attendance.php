<?php

namespace App\Http\Traits;

use App\Enums\AttendanceLogType;
use App\Helpers;
use App\Models\AttendanceLog;
use App\Models\Employee;
use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

trait Attendance
{

    public function getMetaData($data, $date)
    {

        $date = Carbon::parse($date);
        return $this->calculateAttendance($data, $date);
    }

    public function calculateInterval($attendances, $startTime = null, $endTime = null)
    {
        $duration = 0;
        $lastTimeIn = null;
        $totalLate = 0;
        foreach ($attendances as $key => $attendance) {
            $time = Carbon::parse($attendance->time);
            $lateMinutes = 0;
            if ($attendance["log_type"] == AttendanceLogType::TIME_IN->value) {
                $lastTimeIn = $time;
            } else {
                $timeOut = $time;
                if ($lastTimeIn !== null) {
                    $duration += $lastTimeIn->diffInHours($time);
                    if ($time->between($startTime, $endTime)) {
                        if ($lastTimeIn->gt($startTime)) {
                            $lateMinutes = $startTime->diffInMinutes($lastTimeIn);
                        }
                        if ($timeOut !== null && $timeOut->gt($endTime)) {
                            $lateMinutes -= $timeOut->diffInMinutes($endTime);
                        }
                        $lateMinutes = max(0, $lateMinutes);
                        $totalLate += $lateMinutes;
                    }
                }
                $lastTimeIn = null;
            }
        }
        return [
            "total_work_interval" => $duration,
            "total_late_interval" => $totalLate
        ];
    }
    public function calculateOvertimeInterval($attendances, $startTime = null, $endTime = null)
    {
        $duration = 0;
        $lastTimeIn = null;
        $totalLate = 0;

        foreach ($attendances as $key => $attendance) {
            $time = Carbon::parse($attendance->time);
            if ($attendance["log_type"] == AttendanceLogType::TIME_IN->value) {
                $lastTimeIn = $time;
            } else {
                if ($lastTimeIn !== null) {
                    if ($time->between($startTime, $endTime)) {
                        $duration += $lastTimeIn->diffInHours($time);
                        $totalLate += max(0, $lastTimeIn->diffInMinutes($startTime));
                    }
                    $lastTimeIn = null;
                }
            }
        }
        return [
            "total_work_interval" => $duration,
            "total_late_interval" => $totalLate
        ];
    }
    public function calculateAttendance($data, $date)
    {

        $reg = 0;
        $regOvertime = 0;
        $regLate = 0;
        $rest = 0;
        $restOvertime = 0;
        $restLate = 0;
        $regHoliday = 0;
        $regHolidayOvertime = 0;
        $regHolidayLate = 0;
        $specHoliday = 0;
        $specHolidayOvertime = 0;
        $specHolidayLate = 0;
        $totalWorkingHours = 0;
        $totalWorkingLateMinutes = 0;
        $overtimeLate = 0;

        if ($this->hasEvent($data["events"], $date)) {
            $overtime = $this->hasOvertime($data["overtime"], $date);
            $result = $this->calculateInterval($data["attendance"]);
            $regHoliday += $result["total_work_interval"];
            if ($overtime) {
                $startTime = $overtime->overtime_start_time;
                $endTime = $overtime->overtime_end_time;
                $result = $this->calculateOvertimeInterval($data["attendance"], $startTime, $endTime);
                $regHolidayOvertime += $result["total_work_interval"];
                $totalWorkingLateMinutes += $result["total_late_interval"];
                $regHoliday -= $regHolidayOvertime;
            }
        } else {
            if (!$data["schedule"]->isEmpty()) {
                $totalRegularHrs = 0;
                $totalRegularOvertime = 0;
                $lateMinutes = 0;
                $overtime = $this->hasOvertime($data["overtime"], $date);
                foreach ($data["schedule"] as $schedule) {
                    $startTime = $schedule->startTime;
                    $endTime = $schedule->endTime;
                    $result = $this->calculateInterval($data["attendance"], $startTime, $endTime);

                    $totalRegularHrs = $result["total_work_interval"];
                    $lateMinutes += $result["total_late_interval"];
                }
                $reg += $totalRegularHrs;

                if ($overtime) {
                    $startTime = $overtime->overtime_start_time;
                    $endTime = $overtime->overtime_end_time;
                    $result = $this->calculateOvertimeInterval($data["attendance"], $startTime, $endTime);
                    $totalRegularOvertime += $result["total_work_interval"];
                }
                $regOvertime += $totalRegularOvertime;
                $reg -= $regOvertime;
                $regLate += $lateMinutes;
            } else {
                /** REST SCHEDULE */
                $overtime = $this->hasOvertime($data["overtime"], $date);
                $result = $this->calculateInterval($data["attendance"]);
                $rest += $result["total_work_interval"];
                if ($overtime) {
                    $startTime = $overtime->overtime_start_time;
                    $endTime = $overtime->overtime_end_time;
                    $result = $this->calculateOvertimeInterval($data["attendance"], $startTime, $endTime);
                    $restOvertime += $result["total_work_interval"];
                    $rest -= $restOvertime;
                    $overtimeLate += $result["total_late_interval"];
                }
            }
        }


        return [
            "regular" => [
                "reg_hrs" => $reg,
                "overtime" => $regOvertime,
                "late" => $regLate,
            ],
            "rest" => [
                "reg_hrs" => $rest,
                "overtime" => $restOvertime,
                "late" => $restLate,
            ],
            "regular_holidays" => [
                "reg_hrs" => $regHoliday,
                "overtime" => $regHolidayOvertime,
                "late" => $totalWorkingLateMinutes,
            ],
            "special_holidays" => [
                "reg_hrs" => 0,
                "overtime" => 0,
                "late" => 0,
            ],

        ];
    }

    public function hasOvertime($overtime, $date)
    {
        $record = null;
        foreach ($overtime as $overtime) {
            if ($date->isSameDay($overtime->overtime_date)) {
                $record = $overtime;
            } else {
                continue;
            }
        }
        return $record;
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
    public function hasEvent($events, $date)
    {
        $record = null;
        foreach ($events as $event) {
            if ($date->between($event->start_date, $event->end_date)) {
                $record = $event;
            } else {
                continue;
            }
        }
        return $record;
    }
}
