<?php

namespace App\Http\Services\Report;

use App\Models\Department;
use App\Enums\AttendanceSettings;
use App\Enums\EmploymentStatus;
use App\Models\Employee;
use App\Enums\WorkLocation;
use App\Models\Events;
use App\Models\Settings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Helpers;
use App\Enums\AttendanceLogType;
use App\Enums\SalaryRequestType;
use App\Enums\EventTypes;

class AttendanceReportService
{
    public static function employeeAttendance($dtr, $events)
    {
        $fullDayAttendanceCount = 0;
        $absenceCount = 0;
        $late = 0;
        $lateCount = 0;
        $lateDays = [];
        foreach ($dtr as $date => $log) {
            $startDate = Carbon::parse($date);
            if ($events->contains($startDate->toDateString())) {
                continue;
            }
            $schedules = collect($log["metadata"]["summary"]["schedules"]);
            $totalWorkHours = $log["metadata"]["total"]["reg_hrs"];
            $late = $log["metadata"]["total"]["late"];
            foreach ($schedules as $schedule) {
                $startTimeAbsent = strpos($schedule["start_time_log"], "ABSENT") !== false;
                $endTimeAbsent = strpos($schedule["end_time_log"], "ABSENT") !== false;

                if ($startTimeAbsent || $endTimeAbsent) {
                    $absenceCount++;
                    break;
                }
            }

            if ($late > 0 && !in_array($date, $lateDays)) {
                $lateCount++;
                $lateDays[] = $date;
            }

            if ($totalWorkHours > 0) {
                $fullDayAttendanceCount++;
            }
        }
        return [
            "attendanceCount" => $fullDayAttendanceCount,
            "absenceCount" => $absenceCount,
            "lateCount" => $lateCount,
            "lateSched" => $lateDays,
        ];
    }
    public static function calculateWorkRendered($employeeDayData, $date)
    {
        $schedulesSummary = [];
        $duration = 0;
        $totalLate = 0;
        $undertime = 0;
        $chargings = [];
        $chargingNames = [];
        $absentToday = true;
        // SETUP LEAVE FOR CHECKING IF ON LEAVE AND WILL APPLY FOR TODAY
        $leaveUsedToday = array_fill(0, sizeof($employeeDayData["leaves"]), 0);
        // SETTINGS FOR LATES AND ABSENT
        $lateMinsAllowance = Settings::where("setting_name", AttendanceSettings::LATE_ALLOWANCE)->first()->value; // Minutes of late that will be considered as not late
        $lateMinsConsideredAbsent = Settings::where("setting_name", AttendanceSettings::LATE_ABSENT)->first()->value; // Minutes of late that will be considered as absent for a schedule
        $isSunday = $date->dayOfWeek == Carbon::SUNDAY;
        foreach ($employeeDayData['schedules'] as $schedule) {
            $scheduleMetaData = [
                "date" => $date,
                "day_of_week" => $date->dayOfWeek,
                "day_of_week_name" => $date->englishDayOfWeek,
                "id" => $schedule->id,
                "sched_from" => $schedule->schedule_type_name,
                "start_time" => $schedule->startTime,
                "end_time" => $schedule->endTime,
                "start_time_sched" => $schedule->start_time_human,
                "end_time_sched" => $schedule->end_time_human,
                "start_time_log" => $isSunday ? "SUNDAY" : "NO LOG",
                "end_time_log" => $isSunday ? "SUNDAY" : "NO LOG",
                "duration" => 0,
                "late" => 0,
                "undertime" => 0,
            ];
            $charge = null;
            $leaveUsed = false; // To Indicate if leave has been used for the schedule
            $leaveTypeUsed = "WITHOUT PAY";
            // Prepare TIME INS
            $scheduleDateTimeIn = $date->copy()->setTimeFromTimeString($schedule->startTime->format("H:i:s"));
            $timeIn = null;
            // HAS ATTENDANCE LOG
            $attendanceLogIn = $employeeDayData["attendance_logs"]->where(function ($data) use ($schedule) {
                $attendanceTime = Carbon::parse($data->time);
                return $data->log_type == AttendanceLogType::TIME_IN->value &&
                (
                    $attendanceTime->gte($schedule->buffer_time_start_early) ||
                    $attendanceTime->gte($schedule->startTime)
                ) &&
                $attendanceTime->lte($schedule->endTime);
            })->sortBy("time")->values();
            if (sizeof($attendanceLogIn) > 0) {
                $timeIn = $attendanceLogIn->first()->time;
                $scheduleMetaData["start_time_log"] = Carbon::parse($timeIn)->format("h:i A");
                $charge = [
                    "class" => $attendanceLogIn->first()->charging_class,
                    "id" => $attendanceLogIn->first()->charging_id,
                ];
                array_push($chargingNames, $attendanceLogIn->first()->charging_name);
            }
            // CONNECTED TO OVERTIME
            $otAsLogIn = collect($employeeDayData["overtimes"])->filter(function ($otData) use ($schedule) {
                $otSchedOut = $otData->overtime_end_time;
                $schedIn = Carbon::parse($schedule->startTime);
                return $schedIn->equalTo($otSchedOut);
            })->first();
            if ($otAsLogIn) {
                $timeIn = $schedule->startTime;
                $scheduleMetaData["start_time_log"] = "ON OVERTIME";
                $charge = [
                    "class" => $otAsLogIn->charging_class,
                    "id" => $otAsLogIn->charging_id,
                ];
                array_push($chargingNames, $otAsLogIn->charging_name);
            }
            if (!$timeIn) {
                // is On Travel Order
                $travelOrderAsLogIn = $employeeDayData["travel_orders"]->filter(function ($trOrd) use ($scheduleDateTimeIn) {
                    return $trOrd->datetimeIsApplicable($scheduleDateTimeIn);
                })->first();
                if (!$timeIn && $travelOrderAsLogIn) {
                    $timeIn = $schedule->startTime;
                    $scheduleMetaData["start_time_log"] = "ON TRAVEL ORDER";
                    $charge = [
                        "class" => $travelOrderAsLogIn->charge_type,
                        "id" => $travelOrderAsLogIn->charge_id,
                    ];
                    array_push($chargingNames, $travelOrderAsLogIn->charging_designation);
                }
            }
            // PREPARE TIME OUTS
            $scheduleDateTimeOut = $date->copy()->setTimeFromTimeString($schedule->endTime->format("H:i:s"));
            $timeOut = null;
            $attendanceLogOut = $employeeDayData["attendance_logs"]->where(function ($data) use ($schedule) {
                $attendanceTime = Carbon::parse($data->time);
                return $data->log_type == AttendanceLogType::TIME_OUT->value &&
                $attendanceTime->gte($schedule->startTime) &&
                (
                    $attendanceTime->lte($schedule->buffer_time_end_late) ||
                    $attendanceTime->lte($schedule->endTime)
                );
            })->sortBy("time")->values();
            if (sizeof($attendanceLogOut) > 0) {
                $timeOut = $attendanceLogOut->last()->time;
                $scheduleMetaData["end_time_log"] = Carbon::parse($timeOut)->format("h:i A");
            }
            // CONNECTED TO OVERTIME
            $otAsLogOut = collect($employeeDayData["overtimes"])->filter(function ($otData) use ($schedule) {
                $otSchedIn = $otData->overtime_start_time;
                $schedOut = Carbon::parse($schedule->endTime);
                return $schedOut->equalTo($otSchedIn);
            })->first();
            if ($otAsLogOut) {
                $timeOut = $schedule->endTime;
                $scheduleMetaData["end_time_log"] = "ON OVERTIME";
            }
            if (!$timeOut) {
                // is On Travel Order
                $travelOrderAsLogOut = $employeeDayData["travel_orders"]->filter(function ($trOrd) use ($scheduleDateTimeOut) {
                    return $trOrd->datetimeIsApplicable($scheduleDateTimeOut);
                })->first();
                if (!$timeOut && $travelOrderAsLogOut) {
                    $timeOut = $schedule->endTime;
                    $scheduleMetaData["end_time_log"] = "ON TRAVEL ORDER";
                }
            }
            if (!$timeIn || !$timeOut) {
                // Is On Leave
                foreach ($employeeDayData["leaves"] as $index => $leave) {
                    // Logic to set Time In & Out if On Leave
                    // Only sets time if on leave with pay
                    // Deduct half day Used to leave for a leave
                    // display leave name for any type of leave
                    $leaveOnDayDuration = in_array($date->format('Y-m-d'), array_keys($leave->daily_date_durations)) ? $leave->daily_date_durations[$date->format('Y-m-d')] : 0;
                    if (
                        (
                            $leaveOnDayDuration >= 1 ||
                            $leaveUsedToday[$index] < $leaveOnDayDuration
                        )
                    ) {
                        if ($leave->with_pay) {
                            $leaveUsed = true;
                            $leaveTypeUsed = "WITH PAY";
                            $timeIn = $schedule->startTime;
                            $timeOut = $schedule->endTime;
                        }
                        $leaveUsedToday[$index] += 0.5;
                        $scheduleMetaData["start_time_log"] = $leave->leave->leave_name . " - " . $leaveTypeUsed;
                        $scheduleMetaData["end_time_log"] = $leave->leave->leave_name . " - " . $leaveTypeUsed;
                        $charge = [
                            "class" => $leave->charging_class,
                            "id" => $leave->charging_id,
                        ];
                        array_push($chargingNames, $leave->charging_name);
                        break;
                    }
                }
                if (!$leaveUsed) {
                    array_push($schedulesSummary, $scheduleMetaData);
                    continue;
                }
            }
            $in = Carbon::parse($timeIn);
            $out = Carbon::parse($timeOut);
            $startTime = Carbon::parse($schedule->startTime);
            $endTime = Carbon::parse($schedule->endTime);
            $dtrIn = $in->gt($startTime) ? $in : $startTime;
            $dtrOut = $out->gt($endTime) ? $endTime : $out;
            if ($in->gt($schedule->startTime)) {
                $lateMinutes = $startTime->diffInMinutes($in);
                if ($lateMinutes <= $lateMinsAllowance) {
                    $dtrIn = $startTime;
                    $lateMinutes = 0;
                }
                if ($lateMinutes >= $lateMinsConsideredAbsent) {
                    $dtrIn = $dtrOut;
                }
                $totalLate += $lateMinutes;
                $scheduleMetaData["late"] = $lateMinutes;
            }
            if ($endTime->gt($out)) {
                $undertimeMinutes = $out->diffInMinutes($endTime);
                $undertime += $undertimeMinutes;
                $scheduleMetaData["undertime"] = $undertimeMinutes;
            }
            $currentDuration = round($dtrIn->diffInMinutes($dtrOut) / 60, 2);
            $currentDuration = $currentDuration > 0 ? $currentDuration : 0;
            $duration += $currentDuration;
            array_push($chargings, [
                "model" => $charge['class'] ?? Department::class,
                "id" => $charge['id'] ?? 4,
                "hrs_worked" => $currentDuration,
            ]);
            $scheduleMetaData["duration"] = $currentDuration;
            array_push($schedulesSummary, $scheduleMetaData);
            $absentToday = false;
        }

        if (self::getHoliday($employeeDayData["events"], EventTypes::REGULARHOLIDAY->value)) {
            $schedulesSummary = collect($schedulesSummary)->map(function ($data) {
                $data["start_time_log"] .= " REGULAR HOLIDAY";
                $data["end_time_log"] .= " REGULAR HOLIDAY";
                return $data;
            })->values();
        } elseif (self::getHoliday($employeeDayData["events"], EventTypes::SPECIALHOLIDAY->value)) {
            $schedulesSummary = collect($schedulesSummary)->map(function ($data) {
                $data["start_time_log"] .= " SPECIAL HOLIDAY";
                $data["end_time_log"] .= " SPECIAL HOLIDAY";
                return $data;
            })->values();
        } elseif ($absentToday && !$isSunday && ((isset($leaveUsed)  && !$leaveUsed) || !isset($leaveUsed))) {
            $schedulesSummary = collect($schedulesSummary)->map(function ($data) {
                $data["start_time_log"] .= " ABSENT";
                $data["end_time_log"] .= " ABSENT";
                return $data;
            })->values();
        }
        $schedulesSummary = collect($schedulesSummary)->sortBy("start_time")->values();
        return  [
            "rendered" => round($duration, 2),
            "late" => $totalLate,
            "undertime" => $undertime,
            "charging" => $chargings,
            "charging_names" => $chargingNames,
            "summary" => $schedulesSummary,
        ];
    }
    public static function getEmployeeDtr($dateFrom, $dateTo, $validate)
    {
        $employees = Employee::with([
            "current_employment",
            'employee_overtime' => function ($query) use ($dateFrom, $dateTo) {
                $query->betweenDates($dateFrom, $dateTo)
                ->with(["charging"]);
            },
            'employee_travel_order' => function ($query) use ($dateFrom, $dateTo) {
                $query->betweenDates(Carbon::parse($dateFrom)->copy()->subDays(7)->format("Y-m-d"), $dateTo);
            },
            'attendance_log' => function ($query) use ($dateFrom, $dateTo) {
                $query->with(["department", "project"])->betweenDates(Carbon::parse($dateFrom)->copy()->subDays(7)->format("Y-m-d"), $dateTo);
            },
            'employee_leave' => function ($query) use ($dateFrom, $dateTo) {
                $query->with(['leave'])->betweenDates($dateFrom, $dateTo);
            },
            'employee_schedule_irregular' => function ($query) use ($dateFrom, $dateTo) {
                $query->betweenDates($dateFrom, $dateTo);
            },
            'employee_schedule_regular' => function ($query) use ($dateFrom, $dateTo) {
                $query->betweenDates($dateFrom, $dateTo);
            },
            'employee_internal' => function ($query) use ($dateFrom, $dateTo) {
                $query
                ->betweenDates($dateFrom, $dateTo)
                ->with([
                    "department_schedule_irregular" => function ($query) use ($dateFrom, $dateTo) {
                        $query->betweenDates($dateFrom, $dateTo);
                    },
                    "department_schedule_regular" => function ($query) use ($dateFrom, $dateTo) {
                        $query->betweenDates($dateFrom, $dateTo);
                    },
                    'projects' => function ($query) use ($dateFrom, $dateTo) {
                        $query->with([
                            "schedule_irregular" => function ($query) use ($dateFrom, $dateTo) {
                                $query->betweenDates($dateFrom, $dateTo);
                            },
                            "schedule_regular" => function ($query) use ($dateFrom, $dateTo) {
                                $query->betweenDates($dateFrom, $dateTo);
                            },
                        ]);
                    },
                ]);
            },
        ]);

        if (array_key_exists("lates-absence", $validate)) {
            $employees->isActive()->whereHas("current_employment", function ($employment) {
                return $employment->where("salary_type", SalaryRequestType::SALARY_TYPE_NON_FIXED->value)
                    ->orWhere("salary_type", SalaryRequestType::SALARY_TYPE_MONTHLY->value)
                    ->orWhere("salary_type", SalaryRequestType::SALARY_TYPE_WEEKLY->value);
            });
        }

        if ($validate["group_type"] != "All") {
            $workLocation = ($validate["group_type"] === 'Department') ? "Office" : "Project Code";
            $type = ($validate["group_type"] === 'Department') ? "department" : "projects";
            $givenId = ($validate["group_type"] === 'Department') ? $validate["department_id"] : $validate["project_id"];

            $employees->whereHas("current_employment", function ($query) use ($workLocation, $type, $givenId) {
                $query->where('work_location', $workLocation)
                    ->whereHas($type, function ($query) use ($type, $givenId) {
                        if ($givenId) {
                            if ($type === "department") {
                                $query->where("departments.id", $givenId);
                            }
                            if ($type === "projects") {
                                $query->where("projects.id", $givenId);
                            }
                        }
                    });
            });
        }

        return $employees->get();
    }
    public static function getEvents($dateFrom, $dateTo)
    {
        $events = Events::betweenDates($dateFrom, $dateTo)->get();
        return $events;
    }
    public static function processEmployeeDtr($employeeDatas, $dateFrom, $dateTo, $payrollCharging = null)
    {
        $periodDates = Helpers::dateRange([
            'period_start' => $dateFrom, 'period_end' => $dateTo
        ]);
        return collect($periodDates)->groupBy("date")->map(function ($val, $date) use ($employeeDatas, $payrollCharging) {
            $carbonDate = Carbon::parse($date);
            // Get applied Schedule for date
            $appliedDateInternal = self::getAppliedDateInternal($employeeDatas, $carbonDate);
            $appliedDateSchedule = self::getAppliedDateSchedule($employeeDatas, $carbonDate);
            $appliedDateOvertime = self::getAppliedDateOvertime($employeeDatas, $carbonDate);
            $appliedDateAttendanceLogs = self::getAppliedDateAttendanceLogs($employeeDatas, $carbonDate);
            $appliedDateTravelOrders = self::getAppliedDateTravelOrders($employeeDatas, $carbonDate);
            $appliedDateLeaves = self::getAppliedDateLeaves($employeeDatas, $carbonDate);
            $appliedDateEvents = self::getAppliedDateEvents($employeeDatas, $carbonDate);
            $dateDataForProcessing = [
                "date" => $date,
                "employee" => $employeeDatas,
                "internal" => $appliedDateInternal,
                "schedules" => $appliedDateSchedule,
                "overtimes" => $appliedDateOvertime,
                "attendance_logs" => $appliedDateAttendanceLogs,
                "travel_orders" => $appliedDateTravelOrders,
                "leaves" => $appliedDateLeaves,
                "events" => $appliedDateEvents,
            ];
            $processedMetaData = self::calculateDateAttendanceMetaData($dateDataForProcessing, $carbonDate, $payrollCharging);
            return [
                "date" => $date,
                "internal" => $appliedDateInternal,
                "schedules" => $appliedDateSchedule,
                "overtimes" => $appliedDateOvertime,
                "attendance_logs" => $appliedDateAttendanceLogs,
                "travel_orders" => $appliedDateTravelOrders,
                "leaves" => $appliedDateLeaves,
                "events" => $appliedDateEvents,
                "metadata" => $processedMetaData,
            ];
        });
    }
    public static function getAppliedDateInternal($employeeDatas, $date)
    {
        return $employeeDatas["internals"]->where(function ($data) use ($date) {
            return $date->gte($data->date_from) &&
            (
                $date->lte($data->date_to) ||
                is_null($data->date_to)
            );
        })->first();
    }
    public static function getAppliedDateSchedule($employeeDatas, $date)
    {
        $schedule = $employeeDatas["employee_schedules_irregular"]->where(function ($data) use ($date) {
            return $date->eq($data->startRecur);
        })->values();
        if ($schedule && sizeof($schedule) > 0) {
            return $schedule;
        }
        $schedule = $employeeDatas["employee_schedules_regular"]->where(function ($data) use ($date) {
            return $date->gte($data->startRecur) &&
            in_array((string)$date->dayOfWeek, $data->daysOfWeek ?? []) &&
            (
                is_null($data->endRecur) ||
                $date->lt($data->endRecur)
            );
        })->values();
        if ($schedule && sizeof($schedule) > 0) {
            return $schedule;
        }
        $currentInternalOnDate = $employeeDatas["employee"]->employee_internal->where(function ($data) use ($date) {
            return $date->gte($data->date_from) &&
            (
                $date->lte($data->date_to) ||
                is_null($data->date_to)
            );
        })->first();
        $currentWorkLocation = $currentInternalOnDate->work_location ?? "";
        if ($currentWorkLocation == WorkLocation::OFFICE->value) {
            $schedule = $currentInternalOnDate['department_schedule_irregular']->where(function ($data) use ($date) {
                return $date->eq($data->startRecur);
            })->values();
            if ($schedule && sizeof($schedule) > 0) {
                return $schedule;
            }
            $schedule = $currentInternalOnDate['department_schedule_regular']->where(function ($data) use ($date) {
                return $date->gte($data->startRecur) &&
                in_array((string)$date->dayOfWeek, $data->daysOfWeek ?? []) &&
                (
                    is_null($data->endRecur) ||
                    $date->lt($data->endRecur)
                );
            })->values();
            if ($schedule && sizeof($schedule) > 0) {
                return $schedule;
            }
        } elseif ($currentWorkLocation == WorkLocation::PROJECT->value) {
            $latestProject = $currentInternalOnDate->projects()->orderBy('id', 'desc')->first();
            $schedule = $latestProject?->schedule_irregular->where(function ($data) use ($date) {
                return $date->eq($data->startRecur);
            })->values();
            if ($schedule && sizeof($schedule) > 0) {
                return $schedule;
            }
            $schedule = $latestProject?->schedule_regular->where(function ($data) use ($date) {
                return $date->gte($data->startRecur) &&
                in_array((string)$date->dayOfWeek, $data->daysOfWeek ?? []) &&
                (
                    is_null($data->endRecur) ||
                    $date->lt($data->endRecur)
                );
            })->values();
            if ($schedule && sizeof($schedule) > 0) {
                return $schedule;
            }
        }
        return collect([]);
    }
    public static function getAppliedDateOvertime($employeeDatas, $date)
    {
        return $employeeDatas["overtimes"]->where(function ($data) use ($date) {
            return $date->eq($data->overtime_date);
        })->values();
    }
    public static function getAppliedDateAttendanceLogs($employeeDatas, $date)
    {
        return $employeeDatas["attendanceLogs"]->where(function ($data) use ($date) {
            return $date->eq($data->date);
        })->values();
    }
    public static function getAppliedDateTravelOrders($employeeDatas, $date)
    {
        return $employeeDatas["travel_orders"]->where(function ($data) use ($date) {
            return $date->gte($data->date_of_travel) && $date->lte($data->date_time_end);
        })->values();
    }
    public static function getAppliedDateLeaves($employeeDatas, $date)
    {
        return $employeeDatas["leaves"]->where(function ($data) use ($date) {
            return $date->gte($data->date_of_absence_from) && $date->lte($data->date_of_absence_to);
        })->values();
    }
    public static function getAppliedDateEvents($employeeDatas, $date)
    {
        return $employeeDatas["events"]->where(function ($data) use ($date) {
            return $date->gte($data->start_date) && $date->lte($data->end_date);
        })->values();
    }
    public static function calculateDateAttendanceMetaData($employeeDayData, $date, $payrollCharging = null)
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
            "total" => [
                "reg_hrs" => 0,
                "overtime" => 0,
                "late" => 0,
                "undertime" => 0,
            ],
            "summary" => [
                "schedules" => [],
                "overtimes" => [],
                "charging_names" => "",
            ]
        ];
        if (!$employeeDayData["internal"]) {
            return $metaResult;
        }
        $employmentStatusOnDate = $employeeDayData["internal"]->employment_status;
        $daySchedulesDuration = collect($employeeDayData["schedules"])->sum("duration_hours");
        $workRendered = self::calculateWorkRendered($employeeDayData, $date);
        $overtimeRendered = self::calculateOvertimeRendered($employeeDayData, $date);
        $type = "rest";
        $regularHoliday = self::getHoliday($employeeDayData["events"], EventTypes::REGULARHOLIDAY->value, 1);
        $specialHoliday = self::getHoliday($employeeDayData["events"], EventTypes::SPECIALHOLIDAY->value, 1);
        if (!is_null($regularHoliday)) { // Regular Holiday
            $type = "regular_holidays";
            if ($employmentStatusOnDate == EmploymentStatus::REGULAR->value) {
                // REGULAR EMPLOYEE
                if ($regularHoliday->with_work == 0) {
                    // WITHOUT WORK
                    $metaResult["regular"]["reg_hrs"] += $daySchedulesDuration;
                    array_push(
                        $metaResult["charging"]["regular"]["reg_hrs"],
                        [
                            "model" => $payrollCharging ? $payrollCharging["type"] : Department::class,
                            "id" => $payrollCharging ? $payrollCharging["id"] : 4,
                            "hrs_worked" => $daySchedulesDuration,
                        ],
                    );
                } else {
                    // WITH WORK
                    $metaResult["regular"]["reg_hrs"] += $workRendered["rendered"];
                    array_push(
                        $metaResult["charging"]["regular"]["reg_hrs"],
                        [
                            "model" => $payrollCharging ? $payrollCharging["type"] : Department::class,
                            "id" => $payrollCharging ? $payrollCharging["id"] : 4,
                            "hrs_worked" => $workRendered["rendered"],
                        ],
                    );
                }
            } else {
                // NON-REGULAR EMPLOYEES
                if (self::hasAttendanceTravelOnDate($employeeDayData["employee"], $regularHoliday->attendance_date)) {
                    $metaResult["regular"]["reg_hrs"] += $daySchedulesDuration;
                    array_push(
                        $metaResult["charging"]["regular"]["reg_hrs"],
                        [
                            "model" => $payrollCharging ? $payrollCharging["type"] : Department::class,
                            "id" => $payrollCharging ? $payrollCharging["id"] : 4,
                            "hrs_worked" => $daySchedulesDuration,
                        ],
                    );
                }
            }
        } elseif (!is_null($specialHoliday)) { // Special Holiday
            $type = "special_holidays";
            if ($employmentStatusOnDate == EmploymentStatus::REGULAR->value && $specialHoliday->with_work == 0) {
                // REGULAR EMPLOYEE AND EVENT WITHOUT WORK
                // TOTAL DAY SCHEDULE - WORK RENDERED = regular
                $metaResult["regular"]["reg_hrs"] += $daySchedulesDuration - $workRendered["rendered"];
                array_push(
                    $metaResult["charging"]["regular"]["reg_hrs"],
                    [
                        "model" => $payrollCharging ? $payrollCharging["type"] : Department::class,
                        "id" => $payrollCharging ? $payrollCharging["id"] : 4,
                        "hrs_worked" => $daySchedulesDuration - $workRendered["rendered"],
                    ],
                );
            }
            // ELSE WITH WORK WILL USE THE WORKRENDERED
        } elseif ($date->dayOfWeek === Carbon::SUNDAY) { // Rest Day
            $type = "rest";
        } else { // Regular Work Day
            $type = "regular";
        }
        $metaResult[$type]["reg_hrs"] += $workRendered["rendered"];
        $metaResult[$type]["overtime"] += $overtimeRendered["rendered"];
        $metaResult[$type]["late"] += $workRendered["late"];
        $metaResult[$type]["undertime"] += $workRendered["undertime"];
        array_push($metaResult["charging"][$type]["reg_hrs"], ...$workRendered["charging"]);
        array_push($metaResult["charging"][$type]["overtime"], ...$overtimeRendered["charging"]);
        $metaResult["total"]["reg_hrs"] = $workRendered["rendered"];
        $metaResult["total"]["overtime"] = $overtimeRendered["rendered"];
        $metaResult["total"]["late"] = $workRendered["late"];
        $metaResult["total"]["undertime"] = $workRendered["undertime"];
        array_push($metaResult["summary"]["schedules"], ...$workRendered["summary"]); // Here shows schedules with
        array_push($metaResult["summary"]["overtimes"], ...$overtimeRendered["summary"]);
        $chargingNames = array_unique(array_merge($workRendered["charging_names"], $overtimeRendered["charging_names"]));
        $metaResult["summary"]["charging_names"] = implode(", ", $chargingNames); // Here shows schedules with
        return $metaResult;
    }
    public static function calculateOvertimeRendered($employeeDayData, $date)
    {
        $otSchedulesSummary = [];
        $totalHrsWorked = 0;
        $chargings = [];
        $chargingNames = [];
        foreach ($employeeDayData["overtimes"] as $overtime) {
            $dateTimeInSchedule = Carbon::parse($overtime->overtime_date)->setTimeFromTimeString($overtime->overtime_start_time->format("H:i:s"));
            $dateTimeOutSchedule = Carbon::parse($overtime->overtime_date)->setTimeFromTimeString($overtime->overtime_end_time->format("H:i:s"));
            $overtimeMetaData = [
                "date" => $date,
                "day_of_week" => $date->dayOfWeek,
                "day_of_week_name" => $date->englishDayOfWeek,
                "id" => $overtime->id,
                "start_time" => $overtime->overtime_start_time,
                "start_time_v2" => $dateTimeInSchedule,
                "start_time_buffer" => $overtime->buffer_time_start_early,
                "start_time_sched" => $overtime->start_time_human,
                "start_time_log" => $date->dayOfWeek == Carbon::SUNDAY ? "SUNDAY" : "NO LOG",
                "end_time" => $overtime->overtime_end_time,
                "end_time_v2" => $dateTimeOutSchedule,
                "end_time_buffer" => $overtime->buffer_time_end_late,
                "end_time_sched" => $overtime->end_time_human,
                "end_time_log" => $date->dayOfWeek == Carbon::SUNDAY ? "SUNDAY" : "NO LOG",
                "duration" => $overtime->total_hour_duration,
                "late" => 0,
                "undertime" => 0,
            ];
            // PREPARE TIME INS
            $appliedIn = null;
            $attendanceLogIn = $employeeDayData["attendance_logs"]->where(function ($data) use ($overtime) {
                $attendanceTime = Carbon::parse($data->time);
                return $data->log_type == AttendanceLogType::TIME_IN->value &&
                (
                    $attendanceTime->gte($overtime->buffer_time_start_early) ||
                    $attendanceTime->gte($overtime->overtime_start_time)
                ) &&
                $attendanceTime->lte($overtime->overtime_end_time);
            })->sortBy("time")->values();
            if (sizeof($attendanceLogIn) > 0) {
                $appliedIn = $attendanceLogIn->first()->time;
                $overtimeMetaData["start_time_log"] = Carbon::parse($appliedIn)->format("h:i A");
            }
            $hasSchedStart = collect($employeeDayData["schedules"])->contains(function ($schedData) use ($overtime) {
                $schedOut = $schedData->endTime;
                $otIn = Carbon::parse($overtime->overtime_start_time);
                return $otIn->equalTo($schedOut);
            });
            if ($hasSchedStart) {
                $appliedIn = $dateTimeInSchedule;
                $overtimeMetaData["start_time_log"] = "FROM SCHEDULE";
            }
            if (!$appliedIn) {
                // is On Travel Order
                $onTravelOrder = sizeof($employeeDayData["travel_orders"]->filter(function ($trOrd) use ($dateTimeInSchedule) {
                    return $trOrd->datetimeIsApplicable($dateTimeInSchedule);
                })) > 0;
                if (!$appliedIn && $onTravelOrder) {
                    $appliedIn = $overtime->overtime_start_time;
                    $overtimeMetaData["start_time_log"] = "ON TRAVEL ORDER";
                }
            }
            // PREPARE TIME OUTS
            $appliedOut = null;
            $attendanceLogOut = $employeeDayData["attendance_logs"]->where(function ($data) use ($overtime) {
                $attendanceTime = Carbon::parse($data->time);
                return $data->log_type == AttendanceLogType::TIME_OUT->value &&
                $attendanceTime->gte($overtime->overtime_start_time) &&
                (
                    $attendanceTime->lte($overtime->buffer_time_end_late) ||
                    $attendanceTime->lte($overtime->overtime_end_time)
                );
            })->sortBy("time")->values();
            if (sizeof($attendanceLogOut) > 0) {
                $appliedOut = $attendanceLogOut->last()->time;
                $overtimeMetaData["end_time_log"] = Carbon::parse($appliedOut)->format("h:i A");
            }
            $hasSchedContinuation = collect($employeeDayData["schedules"])->contains(function ($schedData) use ($overtime) {
                $schedIn = $schedData->startTime;
                $otOut = Carbon::parse($overtime->overtime_end_time);
                return $otOut->equalTo($schedIn);
            });
            if ($hasSchedContinuation) {
                $appliedOut = $dateTimeOutSchedule;
                $overtimeMetaData["end_time_log"] = "FROM SCHEDULE";
            }
            if (!$appliedOut) {
                // is On Travel Order
                $onTravelOrder = sizeof($employeeDayData["travel_orders"]->filter(function ($trOrd) use ($dateTimeOutSchedule) {
                    return $trOrd->datetimeIsApplicable($dateTimeOutSchedule);
                })) > 0;
                if (!$appliedOut && $onTravelOrder) {
                    $appliedOut = $overtime->overtime_end_time;
                    $overtimeMetaData["end_time_log"] = "ON TRAVEL ORDER";
                }
            }
            if (!$appliedIn || !$appliedOut) {
                array_push($otSchedulesSummary, $overtimeMetaData);
                continue;
            }
            $timeIn = Carbon::parse(Carbon::parse($appliedIn)->format("H:i"));
            $timeOut = Carbon::parse(Carbon::parse($appliedOut)->format("H:i"));
            $schedIn = Carbon::parse($overtime->overtime_start_time);
            $schedOut = Carbon::parse($overtime->overtime_end_time);
            $renderIn = $timeIn->lt($schedIn) ? $schedIn : $timeIn;
            $renderOut = $timeOut->gt($schedOut) ? $schedOut : $timeOut;
            $currentOtHrs = floor($renderIn->diffInMinutes($renderOut, false) / 60); // Changed due to OVERTIME IS ONLY COUNTED BY HOUR
            $currentOtHrs = $currentOtHrs > 0 ? $currentOtHrs : 0;
            // $schedTotalHrs = floor($schedIn->diffInHours($schedOut, false));
            // $currentOtHrs -= boolval($overtime->meal_deduction) && $currentOtHrs === $schedTotalHrs ? 1 : 0;
            $currentOtHrs -= boolval($overtime->meal_deduction) && $currentOtHrs >= 3 ? 1 : 0;
            $overtimeMetaData["rendered"] = $currentOtHrs;
            $totalHrsWorked += $currentOtHrs;
            array_push($chargings, [
                "model" => $overtime->charging_class,
                "id" => $overtime->charging_id,
                "hrs_worked" => $currentOtHrs,
            ]);
            array_push($chargingNames, $overtime->charging_name);
            array_push($otSchedulesSummary, $overtimeMetaData);
        }
        return [
            "rendered" => round($totalHrsWorked, 2),
            "charging" => $chargings,
            "charging_names" => $chargingNames,
            "summary" => $otSchedulesSummary,
        ];
    }
    public static function getHoliday($events, $holidayType = null, $withPay = -1, $withWork = -1)
    {
        $pay = $withPay === -1 ? 0 : $withPay;
        $work = $withWork === -1 ? 0 : $withWork;
        $holidays = collect($events)
        ->when(!is_null($holidayType), function ($query) use ($holidayType) {
            return $query->where("event_type", '=', $holidayType);
        })
        ->when($withPay != -1, function ($query) use ($pay) {
            return $query->where("with_pay", '=', $pay);
        })
        ->when($withWork != -1, function ($query) use ($work) {
            return $query->where("with_work", '=', $work);
        })
        ->first();
        return $holidays;
    }
    public static function hasAttendanceTravelOnDate($employeeDatas, $date)
    {
        $date = Carbon::parse($date);
        $attendances = $employeeDatas["attendanceLogs"]->where(function ($data) use ($date) {
            return $date->eq($data->date);
        })->values();
        $travelOrders = $employeeDatas["travel_orders"]->where(function ($data) use ($date) {
            return $date->gte($data->date_of_travel) && $date->lte($data->date_time_end);
        })->values();
        return sizeof($attendances) > 0 || sizeof($travelOrders) > 0;
    }
}
