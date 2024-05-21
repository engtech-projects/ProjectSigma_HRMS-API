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
        return $this->calculateAttendance($data, $date);
    }

    public function calculateWorkRendered($data)
    {
        $attendances = $data["schedules_attendances"];
        $duration = 0;
        $totalLate = 0;
        foreach ($attendances as $attendance) {
            $timeIn = $attendance["applied_ins"];
            $timeOut = $attendance["applied_outs"];

            $in = Carbon::parse($timeIn?->time);
            $out = Carbon::parse($timeOut?->time);
            $startTime = Carbon::parse($attendance["startTime"]);
            $duration += $in->diffInHours($out);
            if ($in->gt($attendance["startTime"])) {
                $lateMinutes = $startTime->diffInMinutes($in);
                $totalLate += $lateMinutes;
            }
        }
        return [
            "rendered" => $duration,
            "late" => $totalLate
        ];
    }


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
                $total += $otVal->overtime_start_time->diffInHours($otVal->overtime_end_time);
            }
        }
        return $total;
    }
    public function calculateAttendance($data, $date)
    {
        $leave = 0;
        $travel = 0;

        $total = $leave + $travel;
        $reg = 0;
        $regOvertime = 0;
        $late = 0;
        $rest = $total;
        $restOvertime = 0;
        $regHoliday = $total;
        $regHolidayOvertime = 0;

        $leave += $this->getTotalRendered($data["leave"], $date);
        $travel += $this->getTotalRendered($data["travel_orders"], $date);
        if (count($data["events"]) > 0) {
            $result = $this->calculateWorkRendered($data);
            $regHoliday += $result["rendered"];
            $regHolidayOvertime += $this->getOvertimeRendered($data["overtime"]);
            $regHoliday += $leave + $travel;
        } else if ($data["schedule"]) {
            $result = $this->calculateWorkRendered($data);
            $reg += $result["rendered"];
            $regOvertime += $this->getOvertimeRendered($data["overtime"]);
            $late += $result["late"];
            $reg += $leave + $travel;
        } else {
            $result = $this->calculateWorkRendered($data);
            $rest += $result["rendered"];
            $rest += $leave + $travel;
        }

        return [
            "regular" => [
                "reg_hrs" => $reg,
                "overtime" => $regOvertime,
                "late" => $late,
            ],
            "rest" => [
                "reg_hrs" => $rest,
                "overtime" => $restOvertime,
                "late" => 0,
            ],
            "regular_holidays" => [
                "reg_hrs" => $regHoliday,
                "overtime" => $regHolidayOvertime,
                "late" => 0,
            ],
            "special_holidays" => [
                "reg_hrs" => 0,
                "overtime" => 0,
                "late" => 0,
            ],

        ];
    }
}
