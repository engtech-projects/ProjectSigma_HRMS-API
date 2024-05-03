<?php

namespace App\Http\Controllers\Actions\Attendance;

use App\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateDtrRequest;
use App\Http\Resources\AttendanceLogResource;
use App\Http\Resources\EmployeeLeaveResource;
use App\Http\Resources\InternalWorkExpResource;
use App\Http\Resources\OvertimeResource;
use App\Http\Services\EmployeeService;
use App\Models\Employee;
use App\Models\EmployeeDTR;
use Illuminate\Http\JsonResponse;


class EmployeeDtrController extends Controller
{

    /**
     * Handle the incoming request.
     */

    protected $employeeService;
    public function __invoke(GenerateDtrRequest $request, EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
        $filters = $request->validated();
        $periodDates = Helpers::dateRange([
            'period_start' => $filters["cutoff_start"], 'period_end' => $filters["cutoff_end"]
        ]);
        $dtr = EmployeeDTR::whereIn('id', $filters['employee_ids'])->get();
        $result = collect($periodDates)->groupBy(function ($date) {
            return $date["date"];
        })->map(function ($date) use ($dtr) {
            $date = $date[0]["date"];
            foreach ($dtr as $value) {
                return $this->employeeService->employeeDTR($value, $date);
            }
        });
        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => $result
        ]);
    }
}
