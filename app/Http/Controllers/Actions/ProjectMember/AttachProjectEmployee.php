<?php

namespace App\Http\Controllers\Actions\ProjectMember;

use Exception;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Exceptions\TransactionFailedException;
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
        try {
            if ($project) {
                DB::transaction(function () use ($project, $attribute) {
                    $project->employee_has_projects()->sync(
                        $attribute['employee_id']
                    );
                });
            } else {
                throw new NotFoundHttpException("Project not found.");
            }
        } catch (Exception $e) {
            throw new TransactionFailedException("Update transaction failed.", 500, $e);
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully updated.',
        ], JsonResponse::HTTP_OK);
    }
}
