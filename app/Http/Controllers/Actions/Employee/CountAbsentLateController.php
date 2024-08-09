<?php

namespace App\Http\Controllers\Actions\Employee;

use App\Enums\AttendanceLogType;
use App\Enums\AttendanceSettings;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\Settings;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class CountAbsentLateController extends Controller
{
    public function __invoke()
    {
        $attendance = [];
        $lateAllowance = Settings::where("setting_name", AttendanceSettings::LATE_ALLOWANCE)->first()->value;
        if (!Cache::has('lateAndAbsent')) {
            $attendance = AttendanceLog::whereBetween('date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->lastOfMonth()
            ])->where('log_type', AttendanceLogType::TIME_IN->value)->with(['department.schedule', 'project.project_schedule'])->get();
            return array_values($attendance->where(function ($attendance) use ($lateAllowance) {
                if ($attendance->department_id != null) {
                    // return true;
                    return sizeof($attendance->department->schedule->where(function ($sched) use ($attendance, $lateAllowance) {
                        // return true;
                        $schedTimeIn = Carbon::parse($sched->startTime);
                        $schedTimeOut = Carbon::parse($sched->endTime);
                        $attendanceTimeIn = Carbon::parse($attendance->time);
                        return $attendanceTimeIn->gt($schedTimeIn->addMinutes($lateAllowance))
                            && $attendanceTimeIn->lt($schedTimeOut)
                            && in_array(Carbon::parse($attendance->date)->dayOfWeek, $sched->daysOfWeek);
                    })) > 0;
                } else {
                    return sizeof($attendance->project->project_schedule->where(function ($sched) use ($attendance, $lateAllowance) {
                        $schedTimeIn = Carbon::parse($sched->startTime);
                        $schedTimeOut = Carbon::parse($sched->endTime);
                        $attendanceTimeIn = Carbon::parse($attendance->time);
                        return $attendanceTimeIn->gt($schedTimeIn->addMinutes($lateAllowance))
                        && $attendanceTimeIn->lt($schedTimeOut)
                        && in_array(Carbon::parse($attendance->date)->dayOfWeek, $sched->daysOfWeek);
                    })) > 0;
                }
            })->countBy("employee_id")->map(function ($val, $key) {
                $emp = Employee::find($key);
                return[
                    'employee_id' => $key,
                    'fullname_first' => $emp->fullname_first,
                    'fullname_last' => $emp->fullname_last,
                    'profile_photo' => $emp->profile_photo(),
                    'lates' => $val
                ];
            })->toArray());
        }
        Cache::store('database')->put('lateAndAbsent', $attendance, 864000);
        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => Cache::get('dashboard'),
        ]);
    }
}
