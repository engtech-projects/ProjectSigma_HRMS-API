<?php

namespace App\Http\Controllers\Actions\ProjectMember;

use App\Http\Controllers\Controller;
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
        $project->project_members = collect($project->project_members)->map(function ($value) {
            return $value;
        });
        $project["project_member_ids"] = collect($project->project_members)->map(function ($member) {
            return $member["id"];
        });

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => $project
        ]);
    }
}
