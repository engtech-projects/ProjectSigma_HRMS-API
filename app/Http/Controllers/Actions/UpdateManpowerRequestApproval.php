<?php

namespace App\Http\Controllers\Actions;

use App\Enums\ManpowerRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\ManpowerRequest;
use App\Traits\HasApproval;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateManpowerRequestApproval extends Controller
{
    use HasApproval;
    /**
     * Handle the incoming request.
     */
    public function __invoke(ManpowerRequest $manpowerRequest)
    {
        $manpowerRequestApproval = collect($manpowerRequest->approvals);
        $result = $this->updateApproval($manpowerRequestApproval, $manpowerRequest, ['status' => ManpowerRequestStatus::APPROVED]);
        if (count($result) > 0) {
            $manpowerRequest->approvals = $result['approvals'];
            $manpowerRequest->save();
            return new JsonResponse(["success" => $result["success"], "message" => $result['message']], JsonResponse::HTTP_OK);
        }
        return new JsonResponse(["success" => false, "message" => "Failed to approve. Your approval is for later or already done."], JsonResponse::HTTP_NOT_FOUND);
    }
}
