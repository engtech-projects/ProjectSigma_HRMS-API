<?php

namespace App\Http\Services\Attendance;

use App\Enums\AttendanceLogType;
use App\Enums\PayrollType;
use App\Helpers;
use App\Models\Employee;
use App\Models\Events;
use App\Models\PayrollRecord;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

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
                $query->betweenDates($dateFrom, $dateTo);
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
            "employee_schedules_irregular" => $employee->employee_schedule_irregular,
            "employee_schedules_regular" => $employee->employee_schedule_regular,
            "project_schedules_irregular" => $employee->employee_has_projects, // To Get Applicable Project to Process Per day
            "project_schedules_regular" => $employee->employee_has_projects, // To Get Applicable Project to Process Per day
            "department_schedules_irregular" => $employee->employee_internal, // To Get Applicable Employment to Process Per day
            "department_schedules_regular" => $employee->employee_internal, // To Get Applicable Employment to Process Per day
            "overtimes" => $employee->employee_overtime,
            "attendanceLogs" => $employee->attendance_log,
            "travel_orders" => $employee->employee_travel_order,
            "leaves" => $employee->employee_leave,
            'events' => Events::betweenDates($dateFrom, $dateTo)->get(),
        ];
        $employee["dtr"] = Self::processEmployeeDtr($employeeDatas, $dateFrom, $dateTo);
        return $employee;
    }

    public static function processEmployeeDtr($employeeDatas, $dateFrom, $dateTo)
    {
        $periodDates = Helpers::dateRange([
            'period_start' => $dateFrom, 'period_end' => $dateTo
        ]);
        return collect($periodDates)->groupBy("date")->map(function ($val, $date) use ($employeeDatas) {
            
            return [
                "date" => $date,
                "schedule" => $employeeDatas['employee_schedules_regular'],
                "metadata" => "WOW",
            ];
        });
    }
}
