<?php

namespace App\Http\Controllers\Actions\Employee;

use App\Enums\InternalWorkExpStatus;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\InternalWorkExperience;

class CountEmployeeDepartmentController extends Controller
{
    public function __invoke()
    {
        $employeeByDepartment = Department::with(['internal_work_exp' => function ($query) {
            return $query->where('status', InternalWorkExpStatus::CURRENT)->get();
        }])->get();
        $employeeNoDepartment = InternalWorkExperience::whereNull('department_id')->where('status', InternalWorkExpStatus::CURRENT)->count();
        $employeeByDepartment = collect($employeeByDepartment)->flatMap(function ($val) {
            return [
                $val->department_name => $val->internal_work_exp->count()
            ];
        });
        // $employeeByDepartment = $employeeByDepartment->flatten();
        $employeeByDepartment->put('NO DEPARTMENT', $employeeNoDepartment);

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => $employeeByDepartment,
        ]);
    }
}
