<?php

namespace App\Http\Services\Attendance;

use App\Enums\AttendanceLogType;
use App\Enums\PayrollType;
use App\Models\Employee;
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
}
