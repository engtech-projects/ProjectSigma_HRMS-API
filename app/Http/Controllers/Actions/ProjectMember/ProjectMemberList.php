<?php

namespace App\Http\Controllers\Actions\ProjectMember;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectEmployeeResource;
use App\Models\Employee;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class ProjectMemberList extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($project)
    {
        $projectEmployees = collect($project->project_has_employees)->map(function ($value) {
            return $value;
        });
        $project["project_member_ids"] = collect($projectEmployees)->map(function ($member) {
            return $member["id"];
        });

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => new ProjectEmployeeResource($project)
        ]);
    }
}
