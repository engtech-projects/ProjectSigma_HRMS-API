<?php

namespace App\Http\Services\Attendance;

use App\Enums\AttendanceLogType;
use App\Enums\EventTypes;
use App\Enums\PayrollType;
use App\Enums\WorkLocation;
use App\Helpers;
use App\Models\Employee;
use App\Models\Events;
use App\Models\PayrollRecord;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;

class AttendanceService
{
    public static function allowanceAttendance(Employee $employee, Carbon $cutoffStart, Carbon $cutoffEnd)
    {
        $employee->load(['attendance_log' => function ($query) use ($cutoffStart, $cutoffEnd) {
            $query->whereBetween('date', [
                $cutoffStart,
                $cutoffEnd
            ])
            ->where('log_type', AttendanceLogType::TIME_IN->value);
        }, 'attendance_log.department.schedule', 'attendance_log.project.project_schedule']);
        return $employee ->attendance_log->groupBy("date")->count();
    }

    // Version 2 Of Generate DTR - Optimized, query all at beginning
    public static function generateDtr($employeeId, $dateFrom, $dateTo)
    {
        $employee = Employee::with([
            'employee_overtime' => function ($query) use ($dateFrom, $dateTo) {
                $query->betweenDates($dateFrom, $dateTo);
            },
            'employee_travel_order' => function ($query) use ($dateFrom, $dateTo) {
                $query->betweenDates($dateFrom, $dateTo);
            },
            'attendance_log' => function ($query) use ($dateFrom, $dateTo) {
                $query->with(["department", "project"])->betweenDates($dateFrom, $dateTo);
            },
            'employee_leave' => function ($query) use ($dateFrom, $dateTo) {
                $query->betweenDates($dateFrom, $dateTo);
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
                ]);
            },
            'employee_has_projects' => function ($query) use ($dateFrom, $dateTo) {
                $query->with([
                    "schedule_irregular" => function ($query) use ($dateFrom, $dateTo) {
                        $query->betweenDates($dateFrom, $dateTo);
                    },
                    "schedule_regular" => function ($query) use ($dateFrom, $dateTo) {
                        $query->betweenDates($dateFrom, $dateTo);
                    },
                ]);
            },
        ])->find($employeeId);
        $employeeDatas = [
            "employee" => $employee,
            "employee_schedules_irregular" => $employee->employee_schedule_irregular,
            "employee_schedules_regular" => $employee->employee_schedule_regular,
            // DEPARTMENT SCHEDULE TO BE TAKEN FROM employee->"employee_has_projects"
            // DEPARTMENT SCHEDULE TO BE TAKEN FROM employee->"employee_internal"
            "overtimes" => $employee->employee_overtime,
            "attendanceLogs" => $employee->attendance_log,
            "travel_orders" => $employee->employee_travel_order,
            "leaves" => $employee->employee_leave,
            'events' => Events::betweenDates($dateFrom, $dateTo)->get(),
        ];
        $employee["dtr"] = self::processEmployeeDtr($employeeDatas, $dateFrom, $dateTo);
        return $employee;
    }

    public static function processEmployeeDtr($employeeDatas, $dateFrom, $dateTo)
    {
        $periodDates = Helpers::dateRange([
            'period_start' => $dateFrom, 'period_end' => $dateTo
        ]);
        return collect($periodDates)->groupBy("date")->map(function ($val, $date) use ($employeeDatas) {
            $carbonDate = Carbon::parse($date);
            // Get applied Schedule for date
            $appliedDateSchedule = self::getAppliedDateSchedule($employeeDatas, $carbonDate);
            $appliedDateOvertime = self::getAppliedDateOvertime($employeeDatas, $carbonDate);
            $appliedDateAttendanceLogs = self::getAppliedDateAttendanceLogs($employeeDatas, $carbonDate);
            $appliedDateTravelOrders = self::getAppliedDateTravelOrders($employeeDatas, $carbonDate);
            $appliedDateLeaves = self::getAppliedDateLeaves($employeeDatas, $carbonDate);
            $appliedDateEvents = self::getAppliedDateEvents($employeeDatas, $carbonDate);
            $dateDataForProcessing = [
                "schedules" => $appliedDateSchedule,
                "overtimes" => $appliedDateOvertime,
                "attendance_logs" => $appliedDateAttendanceLogs,
                "travel_orders" => $appliedDateTravelOrders,
                "leaves" => $appliedDateLeaves,
                "events" => $appliedDateEvents,
            ];
            $processedMetaData = self::calculateDateAttendanceMetaData($dateDataForProcessing, $carbonDate);
            return [
                "date" => $date,
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
        if ($currentInternalOnDate->work_location == WorkLocation::OFFICE->value) {
            $schedule = $currentInternalOnDate['department_schedules_irregular']->where(function ($data) use ($date) {
                return $date->eq($data->startRecur);
            })->values();
            if ($schedule && sizeof($schedule) > 0) {
                return $schedule;
            }
            $schedule = $currentInternalOnDate['department_schedules_regular']->where(function ($data) use ($date) {
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
        if ($currentInternalOnDate->work_location == WorkLocation::PROJECT->value) {
            $latestProject = $employeeDatas["employee"]->employee_has_projects->first();
            $schedule = $latestProject->schedule_irregular->where(function ($data) use ($date) {
                return $date->eq($data->startRecur);
            })->values();
            if ($schedule && sizeof($schedule) > 0) {
                return $schedule;
            }
            $schedule = $latestProject->schedule_regular->where(function ($data) use ($date) {
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
    public static function calculateDateAttendanceMetaData($employeeDayData, $date)
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
            "summary" => [
                "schedules" => [],
                "overtimes" => [],
            ]
        ];
        $workRendered = self::calculateWorkRendered($employeeDayData, $date);
        $overtimeRendered = self::calculateOvertimeRendered($employeeDayData, $date);
        $type = "rest";
        if (sizeof(collect($employeeDayData["events"])->where("with_work", '=', 0)->where("event_type", '=', EventTypes::REGULARHOLIDAY)) > 0) { // Regular Holiday
            $type = "regular_holidays";
        } elseif (sizeof(collect($employeeDayData["events"])->where("with_work", '=', 0)->where("event_type", '=', EventTypes::SPECIALHOLIDAY)) > 0) { // Special Holiday
            $type = "special_holidays";
        } elseif ($date->dayOfWeek === Carbon::SUNDAY) { // Rest Day
            $type = "rest";
        } else { // Regular Work Day
            $type = "regular";
        }
        // $metaResult[$type]["reg_hrs"] += $workRendered["rendered"];
        // $metaResult[$type]["overtime"] += $overtimeRendered["rendered"];
        // $metaResult[$type]["late"] += $workRendered["late"];
        // $metaResult[$type]["undertime"] += $workRendered["undertime"];
        // array_push($metaResult["charging"][$type]["reg_hrs"], ...$workRendered["charging"]);
        // array_push($metaResult["charging"][$type]["overtime"], ...$overtimeRendered["charging"]);
        // $metaResult["total"]["reg_hrs"] = $workRendered["rendered"];
        // $metaResult["total"]["overtime"] = $overtimeRendered["rendered"];
        // $metaResult["total"]["late"] = $workRendered["late"];
        // $metaResult["total"]["undertime"] = $workRendered["undertime"];
        array_push($metaResult["summary"]["schedules"], ...$workRendered["summary"]); // Here shows schedules with
        // array_push($metaResult["summary"]["overtimes"], ...$overtimeRendered["summary"]);
        return $metaResult;
    }
    public static function calculateWorkRendered($employeeDayData, $date)
    {
        $schedulesSummary = [];
        $duration = 0;
        $totalLate = 0;
        $undertime = 0;
        $chargings = [];
        foreach ($employeeDayData['schedules'] as $schedule) {
            $scheduleMetaData = [
                "date" => $date,
                "day_of_week" => $schedule->dayOfWeek,
                "start_time_sched" => $schedule->start_time_human,
                "end_time_sched" => $schedule->end_time_human,
                "start_time_log" => "ABSENT",
                "end_time_log" => "ABSENT",
                "duration" => 0,
                "late" => 0,
                "undertime" => 0,
            ];
            // HAS ATTENDANCE LOG
            $attendanceLogIn = $employeeDayData["attendance_logs"]->where(function ($data) use ($schedule) {
                return $data->log_type == AttendanceLogType::TIME_IN &&
                (
                    $data->time >= $schedule->buffer_time_start_early ||
                    $data->time >= $schedule->startTime
                ) &&
                $data->time <= $schedule->endTime;
            })->values();
            // CONNECTED TO OVERTIME
            $otAsLogIn = collect($employeeDayData["overtimes"])->filter(function ($otData) use ($schedule) {
                $otSchedOut = $otData->overtime_end_time;
                $schedIn = Carbon::parse($schedule->startTime);
                return $schedIn->equalTo($otSchedOut);
            })->first();
            $attendanceLogOut = $employeeDayData["attendance_logs"]->where(function ($data) use ($schedule) {
                return $data->log_type == AttendanceLogType::TIME_OUT &&
                $data->time >= $schedule->startTime &&
                (
                    $data->time <= $schedule->buffer_time_end_late ||
                    $data->time <= $schedule->endTime
                );
            })->values();
            array_push($schedulesSummary, $scheduleMetaData);
        }
        return  [
            "rendered" => round($duration, 2),
            "late" => $totalLate,
            "undertime" => $undertime,
            "charging" => $chargings,
            "summary" => $schedulesSummary,
        ];
    }
    public static function calculateOvertimeRendered($employeeDayData, $date)
    {
        return [];
    }
}
