<?php

namespace App\Http\Controllers\Actions\LeaveRequest;

use App\Models\EmployeeLeaves;
use Illuminate\Http\JsonResponse;
use App\Enums\RequestApprovalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\DenyManpowerApprovalRequest;

class DisapproveLeaveApproval extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DenyManpowerApprovalRequest $request, EmployeeLeaves $EmployeeLeaves)
    {
        $attribute = $request->validated();
        $result = collect($EmployeeLeaves->updateApproval(['status' => RequestApprovalStatus::DENIED, 'remarks' => $attribute['remarks']]));
        return new JsonResponse(["success" => $result["success"], "message" => $result['message']], JsonResponse::HTTP_OK);
    }
}
