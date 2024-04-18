<?php

namespace App\Http\Controllers\Actions\Pan;

use Illuminate\Http\JsonResponse;
use App\Enums\RequestApprovalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\DisapproveApprovalRequest;
use App\Models\EmployeePersonnelActionNoticeRequest;

class DisapprovePanApproval extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DisapproveApprovalRequest $request, EmployeePersonnelActionNoticeRequest $panRequest)
    {
        $attribute = $request->validated();
        $result = collect($panRequest->updateApproval(['status' => RequestApprovalStatus::DENIED, 'remarks' => $attribute['remarks']]));
        return new JsonResponse(["success" => $result["success"], "message" => $result['message']], JsonResponse::HTTP_OK);
    }
}
