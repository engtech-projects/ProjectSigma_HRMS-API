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
use App\Models\Department;
use App\Models\EmployeeLeaves;
use App\Models\Overtime;
use App\Models\Project;
use App\Models\TravelOrder;
use Illuminate\Support\Facades\Log;

trait Attendance
{

    public function getMetaData($data, $date)
    {
        $date = Carbon::parse($date);
        return $this->calculateAttendance($data, $date);
    }
    public function calculateWorkRendered($data)
    {
        $attendanceSchedules = $data["schedules_attendances"];
        $overtime = $data["overtime"];
        $leave = $data["leave"]->filter("with_pay", 1);
        $travelOrder = $data["travel_orders"];
        $duration = 0;
        $totalLate = 0;
        $undertime = 0;
        $lateAllowance = Settings::where("setting_name", AttendanceSettings::LATE_ALLOWANCE)->first()->value;
        $lateAbsent = Settings::where("setting_name", AttendanceSettings::LATE_ABSENT)->first()->value;
        $chargings = [];
        $charge = null;
        $leaveUsedToday = 0;
        foreach ($attendanceSchedules as $schedule) {
            $hasLeaveToday = sizeof($leave) > 1;
            $leaveToday = $hasLeaveToday ? $leave[0] : null;
            $leaveUsed = false;
            $timeIn = $schedule["applied_ins"];
            if (!$timeIn) {
                // Connected to Overtime
                $oTStart = collect($overtime)->filter(function ($otData) use($schedule) {
                    $otSchedOut = $otData['overtime_end_time'];
                    $schedIn = Carbon::parse($schedule["startTime"]);
                    return $schedIn->equalTo($otSchedOut);
                })->first();
                if (!$timeIn && $oTStart) {
                    $charge = AttendanceLog::find($oTStart["applied_in"]?->id)?->charging() ?? $charge ?? Overtime::find($oTStart["id"])?->charging();
                    $timeIn = (object)["time" => $schedule["startTime"]];
                }
                // Is On Leave
                if (!$timeIn && $hasLeaveToday && $leaveUsedToday < $leaveToday->durationForDate) {
                    $leaveUsedToday =+ 0.5;
                    $leaveUsed = true;
                    $charge = $leaveToday->charging();
                    $timeIn = (object)["time" => $schedule["startTime"]];
                }
                // is On Travel Order
                $onTravelOrder = sizeof($travelOrder) > 1;
                if (!$timeIn && $onTravelOrder) {
                    // $charge =  TravelOrder::find($travelOrder["id"]);
                    $timeIn = (object)["time" => $schedule["startTime"]];
                }
            } else {
                // Charge for Attendance Log Time In
                $charge = AttendanceLog::find($timeIn["id"])->charging();
            }
            $timeOut = $schedule["applied_outs"];
            if (!$timeOut) {
                // Connected to Overtime
                $oTContinuation = collect($overtime)->filter(function ($otData) use($schedule) {
                    $otSchedIn = $otData['overtime_start_time'];
                    $schedOut = Carbon::parse($schedule["endTime"]);
                    return $schedOut->equalTo($otSchedIn);
                })->first();
                if(!$timeOut && $oTContinuation) {
                    $charge = AttendanceLog::find($oTContinuation["applied_out"]?->id)?->charging() ?? $charge ?? Overtime::find($oTContinuation["id"])?->charging();
                    $timeOut = (object)["time" => $schedule["endTime"]];
                }
                // Is On Leave
                if (!$timeOut && $hasLeaveToday && !$leaveUsed && $leaveUsedToday < $leaveToday->durationForDate) {
                    $charge = $leaveToday->charging();
                    $leaveUsedToday =+ 0.5;
                    $timeOut = (object)["time" => $schedule["endTime"]];
                }
                if ($leaveUsed) {
                    $charge = $leaveToday->charging();
                    $timeOut = (object)["time" => $schedule["endTime"]];
                }
                // is On Travel Order
                $onTravelOrder = sizeof($travelOrder) > 1;
                if (!$timeOut && $onTravelOrder) {
                    $timeOut = (object)["time" => $schedule["endTime"]];
                }
            }
            if(!$timeIn || !$timeOut){
                continue;
            }
            $in = Carbon::parse($timeIn?->time);
            $out = Carbon::parse($timeOut?->time);
            $startTime = Carbon::parse($schedule["startTime"]);
            $endTime = Carbon::parse($schedule["endTime"]);
            $dtrIn = $in->gt($startTime) ? $in : $startTime;
            $dtrOut = $out->gt($endTime) ? $endTime : $out;

            if ($in->gt($schedule["startTime"])) {
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
            $currentDuration = round($dtrIn->diffInMinutes($dtrOut) / 60, 2);
            $duration += $currentDuration;
            if ($charge) {
                array_push($chargings, [
                    "model" => get_class($charge),
                    "id" => $charge->id,
                    "hrs_worked" => $currentDuration,
                ]);
            }
        }

        return [
            "rendered" => $duration,
            "late" => $totalLate,
            "undertime" => $undertime,
            "charging" => $chargings,
        ];
    }
    public function getOvertimeRendered($data)
    {
        $total = 0;
        $chargings = [];
        $overtime = $data["overtime"];
        $regSchedule = $data["schedules_attendances"];
        $leave = $data["leave"]->filter("with_pay", 1);
        $travelOrder = $data["travel_orders"];
        foreach ($overtime as $otVal) {
            $appliedIn = $otVal["applied_in"];
            if (!$appliedIn) {
                $hasSchedStart = collect($regSchedule)->contains(function ($schedData) use($otVal) {
                    $schedOut = $schedData['endTime'];
                    $otIn = Carbon::parse($otVal["overtime_start_time"]);
                    return $otIn->equalTo($schedOut);
                });
                if ($hasSchedStart) {
                    $appliedIn = (object)["time" => $otVal["overtime_start_time"]];
                }
                // is On Travel Order
                $onTravelOrder = sizeof($travelOrder) > 1;
                if (!$appliedIn && $onTravelOrder) {
                    $appliedIn = (object)["time" => $otVal["overtime_start_time"]];
                }
            }
            $appliedOut = $otVal["applied_out"];
            if ($appliedOut) {
                $hasSchedContinuation = collect($regSchedule)->contains(function ($schedData) use($otVal) {
                    $schedIn = $schedData['startTime'];
                    $otOut = Carbon::parse($otVal["overtime_end_time"]);
                    return $otOut->equalTo($schedIn);
                });
                if($hasSchedContinuation) {
                    $appliedOut = (object)["time" => $otVal["overtime_end_time"]];
                }
                // is On Travel Order
                $onTravelOrder = sizeof($travelOrder) > 1;
                if (!$appliedOut && $onTravelOrder) {
                    $appliedOut = (object)["time" => $otVal["overtime_end_time"]];
                }
            }
            if(!$appliedIn || !$appliedOut){
                continue;
            }
            $timeIn = Carbon::parse($appliedIn->time);
            $timeOut = Carbon::parse($appliedOut->time);
            $schedIn = Carbon::parse($otVal['overtime_start_time']);
            $schedOut = Carbon::parse($otVal['overtime_end_time']);
            $renderIn = $timeIn->lt($schedIn) ? $schedIn : $timeIn;
            $renderOut = $timeOut->gt($schedOut) ? $schedOut : $timeOut;
            $currentOtHrs = floor($renderIn->diffInMinutes($renderOut, false) / 60); // Changed due to OVERTIME IS ONLY COUNTED BY HOUR
            $schedTotalHrs = floor($schedIn->diffInHours($schedOut, false));
            // $currentOtHrs -= boolval($otVal["meal_deduction"]) && $currentOtHrs === $schedTotalHrs ? 1 : 0;
            $currentOtHrs -= boolval($otVal["meal_deduction"]) && $currentOtHrs >= 3 ? 1 : 0;
            $total += $currentOtHrs;
            $charge = Overtime::find($otVal["id"])->charging();
            if ($charge) {
                array_push($chargings, [
                    "model" => get_class($charge),
                    "id" => $charge->id,
                    "hrs_worked" => $currentOtHrs,
                ]);
            }
        }
        return [
            "rendered" => $total,
            "charging" => $chargings,
        ];
    }
    public function calculateAttendance($data, $date)
    {
        $metaResult = [
            "charging" => [
                // Charging Structure for reg_hrs and overtime
                // [
                //     "model" = "" // Department Model or Project Model
                //     "id" = "" // Id for the Model
                //     "hrs_worked" = "" // Hrs Worked
                // ],
                "regular" => [
                    "reg_hrs" => [],
                    "overtime" => [],
                ],
                "rest" => [
                    "reg_hrs" => [],
                    "overtime" => [],
                ],
                "regular_holidays" => [
                    "reg_hrs" => [],
                    "overtime" => [],
                ],
                "special_holidays" => [
                    "reg_hrs" => [],
                    "overtime" => [],
                ],
            ],
            "regular" => [
                "reg_hrs" => 0,
                "overtime" => 0,
                "late" => 0,
                "undertime" => 0,
            ],
            "rest" => [
                "reg_hrs" => 0,
                "overtime" => 0,
                "late" => 0,
                "undertime" => 0,
            ],
            "regular_holidays" => [
                "reg_hrs" => 0,
                "overtime" => 0,
                "late" => 0,
                "undertime" => 0,
            ],
            "special_holidays" => [
                "reg_hrs" => 0,
                "overtime" => 0,
                "late" => 0,
                "undertime" => 0,
            ],
        ];
        $workRendered = $this->calculateWorkRendered($data);
        $overtimeRendered = $this->getOvertimeRendered($data);
        $type = "rest";
        if (sizeof(collect($data["events"])->where("with_work", '=', 1)->where("event_type", '=', EventTypes::REGULARHOLIDAY)) > 0) { // Regular Holiday
            $type = "regular_holidays";
        } else if (sizeof(collect($data["events"])->where("with_work", '=', 1)->where("event_type", '=', EventTypes::SPECIALHOLIDAY)) > 0) { // Special Holiday
            $type = "special_holidays";
        } else if (sizeof($data["schedules_attendances"]) > 0) { // Regular Work Day
            $type = "regular";
        } else { // Rest Day
            $type = "rest";
        }
        $metaResult[$type]["reg_hrs"] += $workRendered["rendered"];
        $metaResult[$type]["overtime"] += $overtimeRendered["rendered"];
        $metaResult[$type]["late"] += $workRendered["late"];
        $metaResult[$type]["undertime"] += $workRendered["undertime"];
        array_push($metaResult["charging"][$type]["reg_hrs"], ...$workRendered["charging"]);
        array_push($metaResult["charging"][$type]["overtime"], ...$overtimeRendered["charging"]);

        return $metaResult;
    }
    function travelCoversTime($travelOrder, $time) {

    }
    function leaveCoversTime($leave, $time) {

    }
}
