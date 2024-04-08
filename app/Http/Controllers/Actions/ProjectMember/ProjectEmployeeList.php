<?php

namespace App\Http\Controllers\Actions\ProjectMember;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ProjectEmployeeList extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($project)
    {
        $employee = $project->project_has_employee->map(function ($employee) {
            return $employee->pivot->employee_id;
        });
        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => $employee
        ]);
    }
}
