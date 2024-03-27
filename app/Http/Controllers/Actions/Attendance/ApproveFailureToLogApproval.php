<?php

namespace App\Http\Controllers\Actions\Attendance;

use App\Models\FailureToLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Enums\RequestApprovalStatus;
use App\Http\Controllers\Controller;

class ApproveFailureToLogApproval extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(FailureToLog $failureToLog)
    {
        $result = $failureToLog->updateApproval(['status' => RequestApprovalStatus::APPROVED]);
        return new JsonResponse(["success" => $result["success"], "message" => $result['message']], $result["status_code"]);;
    }
}
