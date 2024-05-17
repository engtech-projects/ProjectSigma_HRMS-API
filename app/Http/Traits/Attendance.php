<?php

namespace App\Http\Traits;

use App\Enums\AttendanceLogType;
use App\Helpers;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\Events;
use App\Models\Leave;
use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

trait Attendance
{

    public function getMetaData($data, $date)
    {

        $date = Carbon::parse($date);
        /*         $res = $this->calculateEmployeeAttendance($data); */


        return $this->calculateAttendance($data, $date);
    }



    public function getWorkingHoursRendered($attendances, $startTime = null, $endTime = null)
    {
        $totalHrs = 0;
        if ($attendances) {
            $lastTimeIn = $attendances->where('log_type', 'In')->first();
            $lastTimeOut = $attendances->where('log_type', 'Out')->first();
            $time = null;
            if ($lastTimeIn) {
                $time = Carbon::parse($lastTimeIn->time);
            } else {
                $time = Carbon::parse($lastTimeOut->time);
            }
            if ($startTime && $endTime) {
                $timeIn = Carbon::parse($lastTimeIn->time);
                $timeOut = Carbon::parse($lastTimeOut->time);
                dd($timeIn, $time);
                dd($timeIn->diffInHours($time));
            }


            $time = null;
            if ($lastTimeIn) {
                $time = Carbon::parse($lastTimeIn->time);
            } else {
                if ($lastTimeIn) {
                }
            }
        }


        return $total;
    }

    public function calculateWorkRendered($data, $startTime = null, $endTime = null)
    {

        $attendances = $data["attendance"];
        $duration = 0;
        $lastTimeIn = null;
        $totalLate = 0;
        $leave = 0;

        foreach ($attendances as $attendance) {
            $time = Carbon::parse($attendance->time);
            $lateMinutes = 0;
            if ($attendance["log_type"] == AttendanceLogType::TIME_IN->value) {
                $lastTimeIn = $time;
            } else {
                $timeOut = $time;
                if ($lastTimeIn) {
                    $duration += $lastTimeIn->diffInHours($time);
                    if ($lastTimeIn->gt($startTime)) {
                        $lateMinutes = $startTime->diffInMinutes($lastTimeIn);
                    }
                    if ($timeOut !== null && $timeOut->gt($endTime)) {
                        $lateMinutes -= $timeOut->diffInMinutes($endTime);
                    }
                    $totalLate += $lateMinutes;



                    $lastTimeIn = null;
                }
            }
        }
        return [
            "rendered" => $duration,
            "late" => $totalLate
        ];
    }
    public function calculateOvertimeRendered($overtime, $startTime = null, $endTime = null)
    {
        /*  if ($overtime) {
            foreach ($overtime as $otVal) {
                dd($otVal);
                $startTime = $overtime->overtime_start_time;
                $endTime = $overtime->overtime_end_time;
                $result = $this->calculateOvertimeRendered($overtime, $startTime, $endTime);
                $overtimeHrs += $result["rendered"];
                $late += $result["late"];
            }
        }
        return [
            "rendered" => $duration,
            "late" => $totalLate
        ]; */
    }

    /*     public function getTotalOvertimeRendered($overtime, $attendances, $date)
    {
        $overtimeHrs = 0;
        $late = 0;
        if ($overtime) {
            $startTime = $overtime->overtime_start_time;
            $endTime = $overtime->overtime_end_time;
            $result = $this->calculateOvertimeRendered($overtime, $startTime, $endTime);
            $overtimeHrs += $result["rendered"];
            $late += $result["late"];
        }
        return [
            "rendered" => $overtimeHrs,
            "late" => $late
        ];
    } */
    public function getTotalRendered($data, $date)
    {
        $totalHrs = 0;
        if (!empty($data)) {
            foreach ($data as $value) {
                $duration =  0;
                $to = $value->date_of_absence_to;
                if ($value["number_of_days"] && $value["date_of_absence_to"]) {
                    $duration = $value["number_of_days"];
                    $to = $value["date_of_abasence_to"];
                } else {
                    $duration = $value["duration_of_travel"];
                    $to = $value["date_and_time_of_travel"];
                }
                if ($date->lt($to)) {
                    $totalHrs += 8;
                } else if ($date->eq($to)) {
                    if (is_float($duration)) {
                        $decimal = explode(".", (string)$duration);
                        $totalHrs += $decimal[1];
                    } else {
                        $totalHrs += 8;
                    }
                }
            }
        }
        return $totalHrs;
    }
    public function getOvertimeRendered($overtime)
    {
        $total = 0;
        if ($overtime) {
            foreach ($overtime as $otVal) {
                $total += $otVal->overtime_start_time->diffInHours($otVal->overtime_time);
            }
        }
        return $total;
    }
    public function calculateAttendance($data, $date)
    {
        $leave = 0;
        $travel = 0;
        $leave += $this->getTotalRendered($data["leave"], $date);
        $travel += $this->getTotalRendered($data["travel_orders"], $date);
        $total = $leave + $travel;

        $reg = 0;
        $regOvertime = 0;
        $regLate = 0;
        $rest = $total;
        $restOvertime = 0;
        $restLate = 0;
        $regHoliday = $total;
        $regHolidayOvertime = 0;
        $regHolidayLate = 0;
        $totalWorkingLateMinutes = 0;
        $totalLeave = 0;

        if ($this->hasEvent($data["events"], $date)) {
            //REGULAR HOLIDAY WORK
            $result = $this->calculateWorkRendered($data);
            $regHoliday += $result["rendered"];
            $regHolidayOvertime += $this->getOvertimeRendered($data["overtime"]);
            $regHoliday -= $regHolidayOvertime;
        } else {
            if (!$data["schedule"] == null) {
                $regOvertime += $this->getOvertimeRendered($data["overtime"]);
                $totalRegularHrs = 0;
                $totalRegularOvertime = 0;
                $lateMinutes = 0;
                foreach ($data["schedule"] as $schedule) {
                    $startTime = $schedule->startTime;
                    $endTime = $schedule->endTime;
                    $result = $this->calculateWorkRendered($data, $startTime, $endTime);
                    $totalRegularHrs = $result["rendered"];
                    $lateMinutes += $result["late"];
                }
                $reg += $totalRegularHrs;
                $reg += $leave;
                $regOvertime += $totalRegularOvertime;
                $regLate += $lateMinutes;
            } else {
                //REST WORK
                $result = $this->calculateWorkRendered($data);
                $rest += $result["rendered"];
                /*                 $overtime = $this->getTotalOvertimeRendered($data["overtime"], $data["attendance"], $date);
                $restOvertime += $overtime["rendered"];
                $regHolidayLate += $overtime["late"];
                $rest -= $regHolidayOvertime; */
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
    public function hasTravelOrder($travelOrders, $date)
    {
        $travelOrder = null;
        foreach ($travelOrders as $travel) {
            /*  if ($traveDate->isSameDay($date)) {
            } */
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
