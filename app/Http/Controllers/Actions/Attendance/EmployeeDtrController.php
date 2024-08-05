<?php

namespace App\Http\Controllers\Actions\Attendance;

use App\Helpers;
use App\Models\Employee;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

use App\Http\Services\EmployeeService;
use App\Http\Requests\GenerateDtrRequest;


class EmployeeDtrController extends Controller
{

    /**
     * Handle the incoming request.
     */

    protected $employeeService;
    public function __invoke(GenerateDtrRequest $request, EmployeeService $employeeService)
    {
        ini_set('max_execution_time', '999999');
        $this->employeeService = $employeeService;
        $filters = $request->validated();
        $periodDates = Helpers::dateRange([
            'period_start' => $filters["cutoff_start"], 'period_end' => $filters["cutoff_end"]
        ]);
        $employee = Employee::find($filters["employee_id"]);
        $employee["dtr"] = collect($periodDates)->groupBy("date")
            ->map(function ($dtr) use ($employee) {
                $dtr = $this->employeeService->employeeDTR($employee, $dtr[0]["date"]);
                return $dtr;
            });
        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => $employee
        ]);
    }
}
