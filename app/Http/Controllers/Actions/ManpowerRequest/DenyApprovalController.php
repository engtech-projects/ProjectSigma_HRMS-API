<?php

namespace App\Http\Controllers\Actions\ManpowerRequest;

use App\Models\ManpowerRequest;
use Illuminate\Http\JsonResponse;
use App\Enums\RequestApprovalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\DenyManpowerApprovalRequest;

class DenyApprovalController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DenyManpowerApprovalRequest $request, ManpowerRequest $manpowerRequest)
    {
        $attribute = $request->validated();
        $result = collect($manpowerRequest->updateApproval(['status' => RequestApprovalStatus::DENIED, 'remarks' => $attribute['remarks']]));
        return new JsonResponse(["success" => $result["success"], "message" => $result['message']], JsonResponse::HTTP_OK);
    }
}
