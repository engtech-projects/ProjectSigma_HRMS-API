<?php

namespace App\Http\Traits;

use App\Enums\AttendanceLogType;
use App\Enums\AttendanceSettings;
use App\Helpers;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\Events;
use App\Models\Leave;
use App\Models\Settings;
use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Enums\AssignTypes;
use App\Enums\EventTypes;

trait Attendance
{

    public function getMetaData($data, $date)
    {
        $date = Carbon::parse($date);
        return $this->calculateAttendance($data, $date);
    }

    public function getCharging($data, $date)
    {
        $date = Carbon::parse($date);
        return $this->calculateCharging($data, $date);
    }

    public function calculateWorkRendered($data)
    {
        $attendances = $data["schedules_attendances"];
        $duration = 0;
        $totalLate = 0;
        $undertime = 0;
        $lateAllowance = Settings::where("setting_name", AttendanceSettings::LATE_ALLOWANCE)->first()->value;
        $lateAbsent = Settings::where("setting_name", AttendanceSettings::LATE_ABSENT)->first()->value;
        $projects = collect();
        $departments = collect();

        foreach ($attendances as $attendance) {

            $timeIn = $attendance["applied_ins"];
            $timeOut = $attendance["applied_outs"];
            $hasOTContinuation = collect($data['overtime'])->contains(function ($otData) use($attendance) {
                $otSchedIn = $otData['overtime_start_time'];
                $schedOut = Carbon::parse($attendance["endTime"]);
                return $schedOut->equalTo($otSchedIn);
            });
            $hasOTStart = collect($data['overtime'])->contains(function ($otData) use($attendance) {
                $otSchedOut = $otData['overtime_end_time'];
                $schedIn = Carbon::parse($attendance["startTime"]);
                return $schedIn->equalTo($otSchedOut);
            });
            if(!$timeOut && $hasOTContinuation) {
                $timeOut = (object)["time" => $attendance["endTime"]];
            }
            if(!$timeIn && $hasOTStart) {
                $timeIn = (object)["time" => $attendance["startTime"]];
            }
            if(!$timeIn || !$timeOut){
                continue;
            }

            $in = Carbon::parse($timeIn?->time);
            $out = Carbon::parse($timeOut?->time);
            $startTime = Carbon::parse($attendance["startTime"]);
            $endTime = Carbon::parse($attendance["endTime"]);
            $dtrIn = $in->gt($startTime) ? $in : $startTime;
            $dtrOut = $out->gt($endTime) ? $endTime : $out;

            if ($in->gt($attendance["startTime"])) {
                $lateMinutes = $startTime->diffInMinutes($in);
                if ($lateMinutes <= $lateAllowance) {
                    $dtrIn= $startTime;
                    $lateMinutes = 0;
                }
                if ($lateMinutes >= $lateAbsent) {
                    $dtrIn= $dtrOut;
                }
                $totalLate += $lateMinutes;
            }
            if ($endTime->gt($out)) {
                $undertimeMinutes = $out->diffInMinutes($endTime);
                $undertime += $undertimeMinutes;
            }

            $duration += round($dtrIn->diffInMinutes($dtrOut) / 60, 2);

            switch (strtolower($attendance["groupType"])) {
                case strtolower(AssignTypes::DEPARTMENT->value):
                    if($departments->where('id', $attendance["department_id"])->count() === 0){
                        $departments->push([
                            'id' => $attendance["department_id"],
                            "rendered" => $duration,
                            "late" => $totalLate,
                            "undertime" => $undertime,
                        ]);
                    }else{
                        $departments = $departments->filter(function ($data) use ($attendance) {
                            return $data["id"] === $attendance["department_id"];
                        })->map(function ($data) use($duration, $totalLate, $undertime) {
                            $data['rendered'] = $duration;
                            $data['late'] = $totalLate;
                            $data['undertime'] = $undertime;
                            return $data;
                        });
                    }
                    break;
                case strtolower(AssignTypes::PROJECT->value):
                    if($projects->where('id', $attendance["project_id"])->count() === 0){
                        $projects->push([
                            'id' => $attendance["project_id"],
                            "rendered" => $duration,
                            "late" => $totalLate,
                            "undertime" => $undertime,
                        ]);
                    }else{
                        $projects = $projects->filter(function ($data) use ($attendance) {
                            return $data["id"] === $attendance["project_id"];
                        })->map(function ($data) use($duration, $totalLate, $undertime) {
                            $data['rendered'] = $duration;
                            $data['late'] = $totalLate;
                            $data['undertime'] = $undertime;
                            return $data;
                        });
                    }
                    break;
                default:
                    $projects->push([
                        "rendered" => $duration,
                        "late" => $totalLate,
                        "undertime" => $undertime,
                    ]);
                    $departments->push([
                        "rendered" => $duration,
                        "late" => $totalLate,
                        "undertime" => $undertime,
                    ]);
                break;
            }
        }

        return [
            "projects" => $projects,
            "departments" => $departments,
            "rendered" => $duration,
            "late" => $totalLate,
            "undertime" => $undertime,
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
                $appliedOut = $otVal["applied_out"];
                if(!$appliedOut){
                    continue;
                }
                $timeOut = Carbon::parse($appliedOut->time);
                $schedOut = Carbon::parse($otVal['overtime_end_time']);
                $renderIn = Carbon::parse($otVal['overtime_start_time']);
                $renderOut = $timeOut->gt($schedOut) ? $schedOut : $timeOut;
                $total += round($renderIn->diffInMinutes($renderOut) / 60, 2);
            }
        }
        return $total;
    }
    public function calculateAttendance($data, $date)
    {
        $leave = 0;
        $travel = 0;
        $reg = 0;
        $regOvertime = 0;
        $regUndertime = 0;
        $late = 0;
        $rest = 0;
        $restOvertime = 0;
        $restUndertime = 0;
        $regHoliday = 0;
        $regHolidayOvertime = 0;
        $regHolidayUndertime = 0;
        $leave += $this->getTotalRendered($data["leave"], $date);
        $travel += $this->getTotalRendered($data["travel_orders"], $date);
        if (count(collect($data["events"])->where("with_work", '=', 0)) > 0) {
            $result = $this->calculateWorkRendered($data);
            $regHoliday += $result["rendered"] + $leave + $travel;;
            $regHolidayOvertime += $this->getOvertimeRendered($data["overtime"]);
            $regHolidayUndertime += $result["undertime"];
        } else if ($data["schedules_attendances"]) {
            $result = $this->calculateWorkRendered($data);
            $reg += $result["rendered"] + $leave + $travel;
            $regOvertime += $this->getOvertimeRendered($data["overtime"]);
            $late += $result["late"];
            $regUndertime += $result["undertime"];
        } else {
            $result = $this->calculateWorkRendered($data);
            $rest += $result["rendered"] + $leave + $travel;
            $restUndertime += $result["undertime"];
        }

        return [
            "regular" => [
                "reg_hrs" => round($reg, 2),
                "overtime" => round($regOvertime, 2),
                "late" => round($late, 2),
                "undertime" => round($regUndertime, 2),
            ],
            "rest" => [
                "reg_hrs" => round($rest, 2),
                "overtime" => round($restOvertime, 2),
                "late" => 0,
                "undertime" => round($restUndertime, 2),
            ],
            "regular_holidays" => [
                "reg_hrs" => round($regHoliday, 2),
                "overtime" => round($regHolidayOvertime, 2),
                "late" => 0,
                "undertime" => round($regHolidayUndertime, 2),
            ],
            "special_holidays" => [
                "reg_hrs" => 0,
                "overtime" => 0,
                "late" => 0,
                "undertime" => 0,
            ],
        ];
    }

    function calculateCharging($data, $date){

        $leave = 0;
        $travel = 0;
        $reg = 0;
        $regOvertime = 0;
        $regUndertime = 0;
        $late = 0;
        $rest = 0;
        $restOvertime = 0;
        $restUndertime = 0;

        $regHoliday = 0;
        $specHoliday = 0;
        $regHolidayOvertime = 0;
        $regHolidayUndertime = 0;

        $specialholidaycharge = collect();
        $leavecharge = collect();
        $travelcharge = collect();
        $projects = collect();
        $departments = collect();
        $leave += $this->getTotalRendered($data["leave"], $date);
        $travel += $this->getTotalRendered($data["travel_orders"], $date);
        if(count(collect($data["events"])->where("event_type", EventTypes::SPECIALHOLIDAY->value)) > 0){
            $result = $this->calculateWorkRendered($data);
            $specHoliday += $result["rendered"];
            $specialholidaycharge->push([
                "reg_hrs" => $specHoliday,
            ]);
        }
        if (count(collect($data["events"])->where("with_work", '=', 0)) > 0) {
            $result = $this->calculateWorkRendered($data);
            $regHoliday += $result["rendered"] + $leave + $travel;
            $regHolidayOvertime += $this->getOvertimeRendered($data["overtime"]);
            $regHolidayUndertime += $result["undertime"];
            if(count($result["departments"]) > 0){
                $departments->push([
                    'id' => $result["departments"][0][["id"]],
                    "reg_hrs" => 0,
                    "regular_holidays_hrs" => $regHoliday,
                    "regular_holidays_ot_hrs" => $regHolidayOvertime,
                    "overtime" => 0,
                    "late" => 0,
                    "undertime" => 0,
                ]);
            }
            if(count($result["projects"]) > 0){
                $projects->push([
                    'id' => $result["projects"][0][["id"]],
                    "reg_hrs" => 0,
                    "regular_holidays_hrs" => $regHoliday,
                    "regular_holidays_ot_hrs" => $regHolidayOvertime,
                    "overtime" => 0,
                    "late" => 0,
                    "undertime" => 0,
                ]);
            }
        } else if ($data["schedules_attendances"]) {
            $result = $this->calculateWorkRendered($data);
            $reg += $result["rendered"] + $leave + $travel;
            $regOvertime += $this->getOvertimeRendered($data["overtime"]);
            $late += $result["late"];
            $regUndertime += $result["undertime"];

            if(count($result["projects"]) > 0){
                foreach ($result["projects"] as $key) {
                    $projects->push([
                        'id' => $key["id"],
                        "reg_hrs" => $reg,
                        "regular_holidays_hrs" => 0,
                        "regular_holidays_ot_hrs" => 0,
                        "overtime" => $regOvertime,
                        "late" => $late,
                        "undertime" => $regUndertime,
                    ]);
                }
            }

            if(count($result["departments"]) > 0){
                foreach ($result["departments"] as $key) {
                    $departments->push([
                        'id' => $key["id"],
                        "reg_hrs" => $reg,
                        "regular_holidays_hrs" => 0,
                        "regular_holidays_ot_hrs" => 0,
                        "overtime" => $regOvertime,
                        "late" => $late,
                        "undertime" => $regUndertime,
                    ]);
                }
            }

            if(count($result["departments"])==0 && count($result["projects"])==0){
                if($leave > 0 ){
                    $leavecharge->push([
                        "reg_hrs" => $leave,
                    ]);
                }

                if($travel > 0 ){
                    $travelcharge->push([
                        "reg_hrs" => $travel,
                    ]);
                }
            }

        } else {
            $result = $this->calculateWorkRendered($data);
            $rest += $result["rendered"] + $leave + $travel;
            $restUndertime += $result["undertime"];
        }

        return [
            "special_holiday" => $specialholidaycharge,
            "leaves" => $leavecharge,
            "travels" => $travelcharge,
            "projects" => $projects,
            "departments" => $departments,
            "regular" => [
                "reg_hrs" => $reg,
                "overtime" => $regOvertime,
                "late" => $late,
                "undertime" => $regUndertime,
            ],
            "rest" => [
                "reg_hrs" => $rest,
                "overtime" => $restOvertime,
                "late" => 0,
                "undertime" => $restUndertime,
            ],
            "regular_holidays" => [
                "reg_hrs" => $regHoliday,
                "overtime" => $regHolidayOvertime,
                "late" => 0,
                "undertime" => $regHolidayUndertime,
            ],
            "special_holidays" => [
                "reg_hrs" => 0,
                "overtime" => 0,
                "late" => 0,
                "undertime" => 0,
            ],
        ];
    }
}
