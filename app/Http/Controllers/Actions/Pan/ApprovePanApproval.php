<?php

namespace App\Http\Controllers\Actions\Pan;

use App\Traits\HasApproval;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Enums\RequestApprovalStatus;
use App\Http\Controllers\Controller;
use App\Models\EmployeePersonnelActionNoticeRequest;

class ApprovePanApproval extends Controller
{
    use HasApproval;
    /**
     * Handle the incoming request.
     */
    public function __invoke(EmployeePersonnelActionNoticeRequest $panRequest)
    {
        $result = $this->updateApproval($panRequest, ['status' => RequestApprovalStatus::APPROVED]);
        $panRequest->approvals = $result['approvals'];
        $panRequest->save();
        return new JsonResponse(["success" => $result["success"], "message" => $result['message']], $result["status_code"]);;
    }
    
}
