<?php

namespace App\Http\Controllers\Employee;

use App\Enums\AssignTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkLocationEmployeeRequest;
use App\Models\Department;
use App\Models\Project;

class WorkLocationsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(WorkLocationEmployeeRequest $request)
    {
        $validated = $request->validated();
        $data = null;
        if ($validated["type"] == AssignTypes::PROJECT->value) {
            $data = Project::find($validated["project_id"]);
        } else {
            $data = Department::find($validated["department_id"]);
        }
        return response()->json([
            "message" => "Successfully save.",
            "success" => true,
            "data" => $data,
        ]);
    }
}
