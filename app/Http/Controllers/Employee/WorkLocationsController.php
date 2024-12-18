<?php

namespace App\Http\Controllers\Employee;

use App\Enums\AssignTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkLocationEmployeeRequest;
use App\Http\Resources\WorkLocationMembersDepartmentResource;
use App\Http\Resources\WorkLocationMembersProjectResource;
use App\Http\Resources\WorkLocationMembersUnassignedResource;
use App\Models\Department;
use App\Models\Employee;
use App\Models\InternalWorkExperience;
use App\Models\Project;
use Illuminate\Support\Facades\Log;

class WorkLocationsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(WorkLocationEmployeeRequest $request)
    {
        $validated = $request->validated();
        $data = null;
        if (isset($validated["unassigned"]) && $validated["unassigned"]) {
            $data = Employee::isActive()->get()->where("current_assignment_names", "")->values()->all();
            $data = new WorkLocationMembersUnassignedResource($data);
        } else {
            if ($validated["type"] == AssignTypes::PROJECT->value) {
                $data = Project::find($validated["project_id"]);
                $data = new WorkLocationMembersProjectResource($data);
            } else {
                $data = Department::find($validated["department_id"]);
                $data = new WorkLocationMembersDepartmentResource($data);
            }
        }
        return response()->json([
            "message" => "Successfully save.",
            "success" => true,
            "data" => $data,
        ]);
    }
}
