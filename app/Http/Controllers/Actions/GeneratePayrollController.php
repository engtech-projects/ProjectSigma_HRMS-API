<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneratePayrollRequest;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;

class GeneratePayrollController extends Controller
{

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

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => $employees
        ]);
    }
}
