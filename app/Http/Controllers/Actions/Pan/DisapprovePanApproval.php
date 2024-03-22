<?php

namespace App\Http\Controllers\Actions\Pan;

use App\Traits\HasApproval;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Enums\RequestApprovalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\DenyManpowerApprovalRequest;
use App\Http\Services\EmployeePanRequestService;
use App\Models\EmployeePersonnelActionNoticeRequest;

class DisapprovePanApproval extends Controller
{
    use HasApproval;
    /**
     * Handle the incoming request.
     */
    public function __invoke(DenyManpowerApprovalRequest $request, EmployeePersonnelActionNoticeRequest $panRequest)
    {
        $attribute = $request->validated();
        $result = collect($this->updateApproval($panRequest, ['status' => RequestApprovalStatus::DENIED, 'remarks' => $attribute['remarks']]));
        $nextApproval = $this->getNextPendingApproval(collect($panRequest->approvals));

        if (!$nextApproval) {
            return new JsonResponse(["success" => false, "message" => "Request successfully denied."], JsonResponse::HTTP_NOT_FOUND);
        }

        $isApprovalDenied = collect($result['approvals'])->contains(function ($approval) {
            return $approval['status'] === RequestApprovalStatus::DENIED;
        });

        if (!$isApprovalDenied) {
            if ($nextApproval['user_id'] != auth()->user()->id) {
                return new JsonResponse(["success" => false, "message" => "Failed to approve or deny. Your approval is for later or already done."], JsonResponse::HTTP_FORBIDDEN);
            }
        } else {
            return new JsonResponse(["success" => $result["success"], "message" => $result['message']], JsonResponse::HTTP_FORBIDDEN);
        }
        $panRequest->approval = $result["approval"];
        dd($panRequest->approval);
        $panRequest->save();
        return new JsonResponse(["success" => $result["success"], "message" => $result['message']], JsonResponse::HTTP_OK);
    }
}
