<?php

namespace App\Http\Controllers\Actions\ManpowerRequest;

use App\Enums\RequestApprovalStatus;
use App\Enums\ManpowerRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\ManpowerRequest;
use App\Traits\HasApproval;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApproveApprovalController extends Controller
{
    use HasApproval;
    /**
     * Handle the incoming request.
     */
    public function __invoke(ManpowerRequest $manpowerRequest)
    {
        $result = $this->updateApproval($manpowerRequest, ['status' => RequestApprovalStatus::APPROVED]);
        $manpowerRequest->approvals = $result["approvals"];
        $manpowerRequest->save();
        return new JsonResponse(["success" => $result["success"], "message" => $result['message']], $result["status_code"]);
    }
}
