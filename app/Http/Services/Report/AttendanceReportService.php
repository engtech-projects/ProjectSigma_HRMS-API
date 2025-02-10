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
        $overtimeDates = collect($employee->overtimes)->mapWithKeys(function($overtime) {
            return [$overtime->overtime_date => $overtime->overtime_start_time];
        });

        $fullDayAttendanceCount = 0;
        $halfDayAttendanceCount = 0;
        $absenceCount = 0;
        $checkedDates = [];

        foreach ($schedules as $scheduleList) {
            foreach ($scheduleList as $schedule) {
                $startDate = Carbon::parse($schedule['startRecur']);
                $endDate = Carbon::parse($schedule['endRecur']);
                $daysOfWeek = $schedule['daysOfWeek'];

                while ($startDate->lte($endDate)) {
                    if (in_array($startDate->dayOfWeekIso, $daysOfWeek)) {
                        $dateString = $startDate->toDateString();

                        if (isset($checkedDates[$dateString]) || $events->contains($dateString)) {
                            $startDate->addDay();
                            continue;
                        }

                        $checkedDates[$dateString] = true;
                        $logInFound = false;
                        $logOutFound = false;

                        if ($attendanceLogs->has($dateString)) {
                            $logsForDate = $attendanceLogs->get($dateString);

                            foreach ($logsForDate as $log) {
                                if ($log['employee_id'] == $schedule['employee_id']) {
                                    if ($log['log_type'] == 'In') {
                                        $logInFound = true;
                                    } elseif ($log['log_type'] == 'Out') {
                                        $logOutFound = true;
                                    }
                                }
                            }
                        }

                        if ($overtimeDates->has($dateString) && $overtimeDates->get($dateString) == $schedule['startTime']) {
                            $logInFound = true;
                            $logOutFound = true;
                        }

                        if ($logInFound && $logOutFound) {
                            $fullDayAttendanceCount++;
                        } elseif ($logInFound || $logOutFound) {
                            $halfDayAttendanceCount++;
                        } else {
                            $absenceCount++;
                        }
                    }

                    $startDate->addDay();
                }
            }
        }

        return [
            "fullDayAttendanceCount" => $fullDayAttendanceCount,
            "halfDayAttendanceCount" => $halfDayAttendanceCount,
            "absenceCount" => $absenceCount,
            "schedules" => $schedules,
        ];
    }

    public static function getEmployeeDtr($dateFrom, $dateTo)
    {
        $employees = Employee::with([
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
        ])->get();

        return $employees;
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
