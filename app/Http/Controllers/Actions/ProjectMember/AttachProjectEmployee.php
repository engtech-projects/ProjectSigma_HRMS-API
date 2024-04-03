<?php

namespace App\Http\Controllers\Actions\ProjectMember;

use App\Models\Project;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttachProjectEmployeeRequest;

class AttachProjectEmployee extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Project $project, AttachProjectEmployeeRequest $request)
    {
        $attribute = $request->validated();
        $project->employee_has_projects()->sync(
            $attribute['employee_id']
        );

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully updated.',
        ], JsonResponse::HTTP_OK);
    }
}
