<?php

namespace App\Http\Services\Report;

use App\Models\Department;
use App\Models\Employee;
use App\Enums\WorkLocation;
use App\Models\Events;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceReportService
{
    public static function employeeAttendance($employee, $dateFrom, $dateTo, $events, $schedules)
    {

        $attendanceLogs = collect($employee->attendance_log)->groupBy('date');
        $overtimes = $employee->overtimes;
        $fullDayAttendanceCount = 0;
        $absenceCount = 0;

        foreach ($schedules as $date => $scheduleList) {
            $logInFound = false;
            $logOutFound = false;
            $inSchedule = false;
            $startDate = Carbon::parse($date);
            $dayOfWeek = $startDate->dayOfWeekIso;
            foreach ($scheduleList as $schedule) {
                $startTime = Carbon::parse($schedule['start_time_human']);
                $endTime = Carbon::parse($schedule['end_time_human']);
                if (in_array($dayOfWeek, $schedule['daysOfWeek'])) {
                    $inSchedule = true;
                }
                if (isset($attendanceLogs[$date])) {
                    foreach ($attendanceLogs[$date] as $log) {
                        $dateString = $startDate->toDateString();
                        $logTime = Carbon::parse($log['time']);
                        if ($events->contains($dateString)) {
                            continue;
                        }
                        if ($log["date"] == $date) {
                            if ($logTime >= $startTime && $logTime <= $endTime) {
                                if ($log['log_type'] == 'In') {
                                    $logInFound = true;
                                }
                                if ($log['log_type'] == 'Out') {
                                    $logOutFound = true;
                                }
                            }
                        }
                    }
                }
                if ($overtimes) {
                    foreach ($overtimes as $overtime) {
                        if ($overtime['overtime_date'] == $date) {
                            $overtimeStartTime = Carbon::parse($overtime['start_time_human']);
                            $overtimeEndTime = Carbon::parse($overtime['end_time_human']);

                            if ($overtimeStartTime->between($startTime, $endTime)) {
                                $logInFound = true;
                            }
                            if ($overtimeEndTime->greaterThanOrEqualTo($endTime)) {
                                $logOutFound = true;
                            }
                        }
                    }
                }
            }

            if ($inSchedule) {
                if ($logInFound && $logOutFound) {
                    $fullDayAttendanceCount++;
                } elseif ($logInFound || $logOutFound) {
                } else {
                    $absenceCount++;
                }
            }

        }

        return [
            "lateCount" => $fullDayAttendanceCount,
            "attendanceCount" => $fullDayAttendanceCount,
            "absenceCount" => $absenceCount,
            "schedules" => $schedules,
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

        if ($validate["group_type"] != "All") {
            $workLocation = ($validate["group_type"] === 'Department') ? "Office" : "Project Code";
            $type = ($validate["group_type"] === 'Department') ? "department" : "projects";
            $givenId = ($validate["group_type"] === 'Department') ? $validate["department_id"] : $validate["project_id"];

            $employees->whereHas("current_employment", function ($query) use ($workLocation, $type, $givenId) {
                $query->where('work_location', $workLocation)
                    ->whereHas($type, function ($query) use ($type, $givenId) {
                        if($givenId) {
                            if($type === "department"){
                                $query->where("departments.id", $givenId);
                            }
                            if($type === "projects"){
                                $query->where("projects.id", $givenId);
                            }
                        }
                    });
            });
        }

        return $employees->get();
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

    public static function getEvents($dateFrom, $dateTo)
    {
        $events = collect(Events::betweenDates($dateFrom, $dateTo)->get())->flatMap(function($event) {
            return Carbon::parse($event['start_date'])->daysUntil(Carbon::parse($event['end_date']));
        })->map->toDateString();
        return $events;
    }
}
