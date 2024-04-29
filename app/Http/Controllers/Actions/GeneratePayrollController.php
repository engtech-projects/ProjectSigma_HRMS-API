<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneratePayrollRequest;
use App\Http\Traits\Attendance;
use App\Http\Traits\DailyTimeRecord;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;

class GeneratePayrollController extends Controller
{
    use Attendance, DailyTimeRecord;

    /**
     * Handle the incoming request.
     */
    protected $attendance;
    public function __invoke(GeneratePayrollRequest $request)
    {
        $filters = $request->validated();
        $employees = Employee::with([
            'employee_leave' => function ($query) use ($filters) {
                return $query->payrollLeave($filters)->approved();
            },
            'employee_has_overtime' => function ($query) use ($filters) {
                return $query->payrollOvertime($filters)->approved();
            },
            'attendance_log' => function ($query) use ($filters) {
                return $query->payrollAttendanceLog($filters);
            },
            'employee_schedule' => function ($query) use ($filters) {
                return $query->payrollSchedule($filters);
            },
        ])->whereIn('id', $filters['employee_ids'])->get();

        $result = [];
        foreach ($employees as $employee) {
            $dtr = $this->generateDTR($employee, [
                "period_start" => $filters["cutoff_start"],
                "period_end" => $filters["cutoff_end"]
            ]);
            return $dtr;
            $workingInterval = $this->calculateAttendanceLog($employee->attendance_log);
            $workingLateInterval = $this->calculateAttedanceLate($employee->attendance_log, $employee->employee_schedule);

            $schedules = collect($employee->employee_schedule)->groupBy('startRecur');
            return $$result[] = [
                "total_of_hours" => $workingInterval->totalHours,
                "total_of_days" => $workingInterval->totalDays,
                "total_of_minutes" => $workingInterval->totalMinutes,
                "total_of_lates" => $workingLateInterval
            ];
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => $employees
        ]);
    }
}
