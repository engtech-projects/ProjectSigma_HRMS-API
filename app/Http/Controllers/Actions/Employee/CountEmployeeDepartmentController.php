<?php

namespace App\Http\Controllers\Actions\Employee;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\InternalWorkExperience;

class CountEmployeeDepartmentController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $employeeByDepartment = Department::with(['internal_work_exp'])->get();
        $employeeNoDepartment = InternalWorkExperience::whereNull('department_id')->count();
        $employeeByDepartment = collect($employeeByDepartment)->groupBy(function ($department) {
            $deptName = $department->department_name;
            return $deptName;
        })->map(function ($val) {
            return $val[0]->internal_work_exp->count();
        });

        $employeeByDepartment->put('No Department', $employeeNoDepartment);


        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => $employeeByDepartment
        ]);
    }
}
