<?php

namespace App\Http\Controllers\Actions\ManpowerRequest;

use App\Enums\RequestApprovalStatus;
use App\Http\Controllers\Controller;
use App\Models\ManpowerRequest;
use Illuminate\Http\JsonResponse;

class ApproveApproval extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ManpowerRequest $manpowerRequest)
    {
        $result = $manpowerRequest->updateApproval(['status' => RequestApprovalStatus::APPROVED]);
        return new JsonResponse(["success" => $result["success"], "message" => $result['message']], $result["status_code"]);
    }
}
