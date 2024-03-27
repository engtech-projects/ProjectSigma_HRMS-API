<?php

namespace App\Http\Controllers\Actions\Attendance;

use App\Models\FailureToLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Enums\RequestApprovalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\DisapproveApprovalRequest;

class DisapproveFailureToLogApproval extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DisapproveApprovalRequest $request, FailureToLog $failureToLog)
    {
        $attribute = $request->validated();
        $result = collect($failureToLog->updateApproval(['status' => RequestApprovalStatus::DENIED, 'remarks' => $attribute['remarks']]));
        return new JsonResponse(["success" => $result["success"], "message" => $result['message']], JsonResponse::HTTP_OK);
    }
}
