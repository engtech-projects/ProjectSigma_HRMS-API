<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceLogType;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class AbsentController extends Controller
{
    public function getAbsenceThisMonth(Schedule $req, AttendanceLog $log)
    {
        $attendance = AttendanceLog::whereBetween('date', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->lastOfMonth()
        ])->where('log_type', AttendanceLogType::TIME_IN->value)->with(['department.schedule', 'project.project_schedule'])->get();
        return array_values($attendance->where(function($attendance) {
            if ($attendance->department_id != null) {
                return sizeof($attendance->department->schedule) > 0;
            }
        })->countBy("employee_id")->map(function($val, $key) {
            $emp = Employee::find($key);
            return[
                'employee_id' => $key,
                'fullname_first' => $emp->fullname_first,
                'fullname_last' => $emp->fullname_last,
                'profile_photo' => $emp->profile_photo(),
                'absent' => $val
            ];
        })->toArray());
    }
}
