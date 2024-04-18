<?php

namespace App\Http\Controllers\Actions\Pan;

use Illuminate\Http\JsonResponse;
use App\Enums\RequestApprovalStatus;
use App\Http\Controllers\Controller;
use App\Models\EmployeePersonnelActionNoticeRequest;

class ApprovePanApproval extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(EmployeePersonnelActionNoticeRequest $panRequest)
    {
        $result = $panRequest->updateApproval(['status' => RequestApprovalStatus::APPROVED]);
        return new JsonResponse(["success" => $result["success"], "message" => $result['message']], $result["status_code"]);
        ;
    }
}
