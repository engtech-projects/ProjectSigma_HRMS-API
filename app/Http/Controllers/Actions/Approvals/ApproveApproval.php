<?php

namespace App\Http\Controllers\Actions\Approvals;

use App\Enums\ApprovalModels;
use Illuminate\Http\JsonResponse;
use App\Enums\RequestApprovalStatus;
use App\Http\Controllers\Controller;
use App\Models\Users;
use App\Notifications\LeaveRequestApproved;
use App\Notifications\LeaveRequestForApproval;

class ApproveApproval extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($modelType, $model)
    {
        $result = $model->updateApproval(['status' => RequestApprovalStatus::APPROVED]);
        if ($model->getNextPendingApproval()) {
            switch ($modelType) {
                case ApprovalModels::LeaveEmployeeRequest->name:
                    Users::find($model->getNextPendingApproval()->user_id)->notify(new LeaveRequestForApproval($model)); // Notify the next Approval
                    break;
            }
        } else {
            switch ($modelType) {
                case ApprovalModels::LeaveEmployeeRequest->name:
                    Users::find(1)->notify(new LeaveRequestApproved($model)); // Notify Request Creator Request fully Approved
                    break;
            }
        }
        return new JsonResponse(["success" => $result["success"], "message" => $result['message']], $result["status_code"]);;
    }
}
