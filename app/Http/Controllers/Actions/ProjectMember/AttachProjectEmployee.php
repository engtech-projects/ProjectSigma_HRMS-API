<?php

namespace App\Http\Controllers\Actions\ProjectMember;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Exceptions\TransactionFailedException;
use App\Http\Requests\AttachProjectEmployeeRequest;

class AttachProjectEmployee extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($project, AttachProjectEmployeeRequest $request)
    {
        $attribute = $request->validated();
        try {
            DB::transaction(function () use ($project, $attribute) {
                $project->project_has_employees()->sync($attribute["employee_id"]);
            });
        } catch (Exception $e) {
            throw new TransactionFailedException("Update transaction failed.", 500, $e);
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully updated.',
        ], JsonResponse::HTTP_OK);
    }
}
