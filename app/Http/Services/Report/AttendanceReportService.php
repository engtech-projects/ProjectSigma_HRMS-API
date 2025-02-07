<?php

namespace App\Http\Services\Report;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Events;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceReportService
{
    public static function employeeAttendance($employee, $dateFrom, $dateTo, $events)
    {
        $schedules = collect();
        foreach ($employee->employee_internal as $key) {
            if (isset($key)) {
                if (isset($key->department_schedule_irregular)) {
                    $schedules = $schedules->merge($key->department_schedule_irregular);
                }
                if (isset($key->department_schedule_regular)) {
                    $schedules = $schedules->merge($key->department_schedule_regular);
                }
                if (isset($key->projects)) {
                    if (isset($key->projects->schedule_irregular)) {
                        $schedules = $schedules->merge($key->projects->schedule_irregular);
                    }
                    if (isset($key->projects->schedule_regular)) {
                        $schedules = $schedules->merge($key->projects->schedule_regular);
                    }
                }
            }
            if (isset($employee->employee_schedules_irregular)) {
                $schedules = $schedules->merge($employee->employee_schedules_irregular);
            }
            if (isset($employee->employee_schedules_regular)) {
                $schedules = $schedules->merge($employee->employee_schedules_regular);
            }
        }

        $schedules = $schedules->flatten();


        $attendanceLogs = collect($employee->attendance_log)->groupBy('date');
        $overtimeDates = collect($employee->overtimes)->mapWithKeys(function($overtime) {
            return [$overtime->overtime_date => $overtime->overtime_start_time];
        });

        $fullDayAttendanceCount = 0;
        $halfDayAttendanceCount = 0;
        $absenceCount = 0;
        $checkedDates = [];

        foreach ($schedules as $schedule) {
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

    public static function getEvents($dateFrom, $dateTo)
    {
        $events = collect(Events::betweenDates($dateFrom, $dateTo)->get())->flatMap(function($event) {
            return Carbon::parse($event['start_date'])->daysUntil(Carbon::parse($event['end_date']));
        })->map->toDateString();
        return $events;
    }
}
