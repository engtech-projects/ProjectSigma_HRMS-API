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
        /*  $workingInterval = CarbonInterval::seconds($duration); */
        return $duration;
    }
    public function getWorkingIntervalLate()
    {
    }
    public function calculateAttendance($data)
    {
        $regHRS = 0;
        $regHolidayHRS = 0;
        $specHolidayHRS = 0;
        $restDayHRS = 0;
        $duration = 0;
        $lastTimeIn = null;
        $late = 0;
        foreach ($data["attendance"] as $attendance) {
            $time = Carbon::parse($attendance->time);
            if ($attendance["log_type"] == AttendanceLogType::TIME_IN->value) {
                $lastTimeIn = $time;
            } else {
                if ($lastTimeIn !== null) {
                    $lastTimeInDuration = $lastTimeIn->diffInHours($time);
                    $duration += $lastTimeInDuration;

                    if ($time->diffIndays($lastTimeIn) > 0) {
                        $lateDuration = $time->diffInSeconds($time->copy()->startOfDay());
                        $late += $lateDuration;
                    }
                    $lastTimeIn = null;
                }
            }
            $attendanceDate = Carbon::parse($attendance->date);

            dd($this->getSchedule($data["schedule"], $attendance));
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
    /*     public function getWorkingLateInterval($schedule, $attendance)
    {
        $lateDuration = 0;
        foreach ($schedule as $value) {
            $scheduleDate = Carbon::parse($value["startRecur"]);

            if ($scheduleDate->isSameDay($attendance->date)) {
                if ($attendance->log_type == "In") {
                    $timeIn = $attendance->time;
                    $timeOut = null;
                } else {
                    $timeOut = $attendance->time;
                }

                if ($timeOut !== null) {
                    $timeOut = Carbon::parse($attendance->time);
                    if ($timeOut->diffInDays($timeIn) > 0) {

                        $lateHours = $timeOut->diffInSeconds($timeOut->copy()->startOfDay());
                        $lateDuration += $lateHours;
                    }
                }
            }
        }
        return $lateDuration;
    } */
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

    public function getWorkingLate($schedule, $attendance)
    {
        $lastTimeIn = AttendanceLog::where("employee_id", $attendance->id)->where('log_type', 'In')->orderBy('id', 'desc')->first();
        $lastTimeIn = Carbon::parse($lastTimeIn->time);
        $scheduleStartime = Carbon::parse($schedule->startTime);
        return $lastTimeIn->diffInMinutes($scheduleStartime);
    }
    public function getScheduleWorkingHours($schedule, $attendance)
    {
        $lastTimeOut = AttendanceLog::where("employee_id", $attendance->id)->where('log_type', 'Out')->orderBy('id', 'desc')->first();
        $lastTimeOut = Carbon::parse($lastTimeOut->time);
        $scheduleStartime = Carbon::parse($schedule->startTime);
        return $lastTimeOut->diffInHours($scheduleStartime);
    }
    public function getSchedule($schedules, $attendance)
    {
        $totalLate = 0;
        $totalWorkingHours = 0;
        foreach ($schedules as $schedule) {
            $scheduleDate = Carbon::parse($schedule["startRecur"]);
            if ($scheduleDate->isSameDay($attendance->date)) {
                $totalLate += $this->getWorkingLate($schedule, $attendance);
                $totalWorkingHours += $this->getScheduleWorkingHours($schedule, $attendance);
            }
            continue;
        }
        return [
            "total_late" => $totalLate,
            "total_working_hours" => $totalWorkingHours,
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
