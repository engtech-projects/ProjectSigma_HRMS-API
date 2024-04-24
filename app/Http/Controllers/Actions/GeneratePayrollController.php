<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneratePayrollRequest;
use App\Http\Services\AttendanceLogService;
use App\Http\Traits\CalculateAttendance;
use App\Interfaces\Attendance as AttendanceInterface;
use App\Models\Employee;
use App\Models\Schedule as EmployeeSchedule;
use App\repositories\CalculateAttendanceRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeneratePayrollController extends Controller
{

    /**
     * Handle the incoming request.
     */

    /**
     * COMPARE SCHEDULE TO ATTENDANCE LOG IF ADD COUNT FOR THE NUMBER OF DAYS/HOURS
     * HALF DAY - .5
     * LATE - BASE IN TOTAL OF MINUTES/HOUR LATE
     */
    protected $attendanceLogService;
    public function __invoke(GeneratePayrollRequest $request, CalculateAttendanceRepository  $calculateAttendance)
    {

        $attributes = $request->validated();


        $employees = Employee::with([
            'attendance_log',
            'employee_schedule'
        ])->whereIn('id', $attributes['employee_ids'])->get();

        $collection = collect($employees)->map(function ($employee) {
            $employee->total_of_working_days = $this->getNumberOfDays($employee);
            return $employee;
        });
        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => $calculateAttendance->getTotalOfRestDay($employees)
        ]);
    }
    private function getTotalOfWorkingDays($employee)
    {
        CalculateAttendance::totalOfOvertime($employee);
        return $employee->id;
    }
}
