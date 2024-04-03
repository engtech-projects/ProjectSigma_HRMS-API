<?php

namespace App\Http\Controllers\Actions\Approvals;

use Illuminate\Http\JsonResponse;
use App\Enums\RequestApprovalStatus;
use App\Http\Controllers\Controller;

class ApproveApproval extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($modelType, $model)
    {
        $result = $model->updateApproval(['status' => RequestApprovalStatus::APPROVED]);
        return new JsonResponse(["success" => $result["success"], "message" => $result['message']], $result["status_code"]);;
    }
}
