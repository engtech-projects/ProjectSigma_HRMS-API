<?php

namespace App\Http\Controllers\Actions\Attendance;

use App\Helpers;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

use App\Http\Services\EmployeeService;
use App\Http\Requests\GenerateDtrRequest;
use App\Http\Services\Attendance\AttendanceService;

class EmployeeDtrControllerV2 extends Controller
{
    /**
     * Handle the incoming request.
     */

    public function __invoke(GenerateDtrRequest $request, )
    {
        $filters = $request->validated();
        $employeeDtr = AttendanceService::generateDtr($filters["employee_id"], $filters["cutoff_start"], $filters["cutoff_end"]);
        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => $employeeDtr
        ]);
    }
}
