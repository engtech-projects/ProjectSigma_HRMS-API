<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceLogType;
use App\Enums\SalaryRequestType;
use App\Http\Resources\CompressedImageResource;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\Schedule;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

class AbsentController extends Controller
{
    public function getAbsenceThisMonth(Schedule $req, AttendanceLog $log)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now();
        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);
        // Filter the period to include only Sundays
        $sundayDays = $period->filter(function (Carbon $date) {
            return $date->isSunday();
        })->toArray();

        $sundaysThisMonth = sizeof($sundayDays);
        $daysThisMonth = $endOfMonth->format('d');
        $workDaysCount = $daysThisMonth - $sundaysThisMonth;

        $attendance = Employee::whereHas("current_employment", function ($employment) {
            return $employment->where("salary_type", SalaryRequestType::SALARY_TYPE_NON_FIXED->value)
                ->orWhere("salary_type", SalaryRequestType::SALARY_TYPE_MONTHLY->value)
                ->orWhere("salary_type", SalaryRequestType::SALARY_TYPE_WEEKLY->value);
        })
        ->isActive()
        ->with([
            'attendance_log' => function ($query) use ($sundayDays) {
                $query->whereBetween('date', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ])
                ->whereNotIn('date', $sundayDays)
                ->where('log_type', AttendanceLogType::TIME_IN->value);
            },
            'attendance_log.department.schedule',
            'attendance_log.project.project_schedule',
            'company_employments'

        ])
        ->get();

        return $attendance->map(function ($employee) use ($workDaysCount) {
            $attendedDays = $employee->attendance_log->groupBy("date")->count();
            return [
                'fullname_first' => $employee->fullname_first,
                'fullname_last' => $employee->fullname_last,
                'profile_photo' => $employee->profile_photo ? new CompressedImageResource($employee->profile_photo) : null,
                'absent' => $workDaysCount - $attendedDays,
                'workDaysCount' => $workDaysCount,
                'attendDays' => $attendedDays,
            ];
        })
        ->filter(function ($employee) {
            return $employee['absent'] > 0;
        })
        ->sortByDesc("absent")
        ->values()
        ->all();

    }
}
