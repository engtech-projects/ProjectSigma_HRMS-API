<?php

namespace App\Http\Controllers\Actions\ProjectMember;

use App\Exceptions\TransactionFailedException;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttachProjectEmployeeRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AttachProjectEmployee extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($project, AttachProjectEmployeeRequest $request)
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
