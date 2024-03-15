<?php

namespace App\Http\Controllers\Actions;

use App\Enums\ManpowerApprovalStatus;
use App\Enums\ManpowerRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\ManpowerRequest;
use App\Traits\HasApproval;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApproveManpowerRequestApproval extends Controller
{
    use HasApproval;
    /**
     * Handle the incoming request.
     */
    public function __invoke(ManpowerRequest $manpowerRequest)
    {
        $manpowerRequestApproval = collect($manpowerRequest->approvals);
        $result = $this->updateApproval($manpowerRequestApproval, $manpowerRequest, ['status' => ManpowerApprovalStatus::APPROVED]);
        $nextApproval = $this->getNextPendingApproval($manpowerRequestApproval);

        /* if (!$nextApproval) {
            return new JsonResponse(["success" => false, "message" => "Manpower request has already been approved."], JsonResponse::HTTP_FORBIDDEN);
        }
        $lastApproval = $result['approvals']->last();
        if ($lastApproval['status'] === ManpowerApprovalStatus::APPROVED) {
            $manpowerRequest->request_status = ManpowerRequestStatus::APPROVED;
        } */
        $manpowerRequest->approvals = $result['approvals'];
        $manpowerRequest->save();
        return new JsonResponse(["success" => $result["success"], "message" => $result['message']], $result["status_code"]);
    }
}
