<?php

namespace App\Http\Controllers\Actions;

use Illuminate\Http\Request;
use App\Models\ManpowerRequest;
use Illuminate\Http\JsonResponse;
use App\Enums\ManpowerApprovalStatus;
use App\Enums\ManpowerRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\DenyManpowerApprovalRequest;
use App\Traits\HasApproval;

class DenyManpowerRequestApproval extends Controller
{
    use HasApproval;
    /**
     * Handle the incoming request.
     */
    public function __invoke(DenyManpowerApprovalRequest $request, ManpowerRequest $manpowerRequest)
    {
        $attribute = $request->validated();
        $manpowerRequestApproval = collect($manpowerRequest->approvals);
        $result = collect($this->updateApproval($manpowerRequestApproval, $manpowerRequest, ['status' => ManpowerApprovalStatus::DENIED, 'remarks' => $attribute['remarks']]));
        $nextApproval = $this->getNextPendingApproval($manpowerRequestApproval);

        if (!$nextApproval) {
            return new JsonResponse(["success" => false, "message" => "Manpower request successfully denied."], JsonResponse::HTTP_NOT_FOUND);
        }

        $isApprovalDenied = collect($result['approvals'])->contains(function ($approval) {
            return $approval['status'] === ManpowerApprovalStatus::DENIED;
        });

        if (!$isApprovalDenied) {
            if ($nextApproval['user_id'] != auth()->user()->id) {
                return new JsonResponse(["success" => false, "message" => "Failed to approve or deny. Your approval is for later or already done."], JsonResponse::HTTP_FORBIDDEN);
            }
        } else {
            return new JsonResponse(["success" => $result["success"], "message" => $result['message']], JsonResponse::HTTP_FORBIDDEN);
        }
        return new JsonResponse(["success" => $result["success"], "message" => $result['message']], JsonResponse::HTTP_OK);
    }
}
