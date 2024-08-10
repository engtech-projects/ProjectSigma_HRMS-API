<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceLogType;
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

        $attendance = Employee::with(['attendance_log' => function ($query) use ($sundayDays) {
            $query->whereBetween('date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->whereNotIn('date', $sundayDays)
            ->where('log_type', AttendanceLogType::TIME_IN->value);
        }, 'attendance_log.department.schedule', 'attendance_log.project.project_schedule'])
        ->get();

        return $attendance->map(function ($employee) use ($workDaysCount) {
            $attendedDays = $employee->attendance_log->groupBy("date")->count();
            return [
                'fullname_first' => $employee->fullname_first,
                'fullname_last' => $employee->fullname_last,
                'profile_photo' => $employee->profile_photo(),
                'absent' => $workDaysCount - $attendedDays,
                'workDaysCount' => $workDaysCount,
                'attendDays' => $attendedDays,
            ];
        })->sortByDesc("absent");
    }
}
