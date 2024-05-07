<?php

namespace App\Http\Controllers\Actions\Attendance;

use App\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateDtrRequest;
use App\Http\Services\EmployeeService;
use App\Models\Employee;

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
        $employeeDtr = Employee::whereIn('id', $filters['employee_ids'])->get();
        $result = collect($employeeDtr)->map(function ($employee) use ($periodDates, $employeeDtr) {
            $employee["dtr"] = collect($periodDates)->groupBy(function ($date) {
                return $date["date"];
            })->map(function ($date) use ($employeeDtr) {
                $date = $date[0]["date"];
                foreach ($employeeDtr as $value) {
                    return $this->employeeService->employeeDTR($value, $date);
                }
            });
            return $employee;
        });

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => $result
        ]);
    }
}
