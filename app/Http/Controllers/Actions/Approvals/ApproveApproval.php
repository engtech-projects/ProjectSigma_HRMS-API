<?php

namespace App\Http\Controllers\Actions\Approvals;

use App\Enums\ApprovalModels;
use App\Notifications\CashAdvanceApproved;
use App\Notifications\CashAdvanceForApproval;
use App\Notifications\FailureToLogRequestApproved;
use App\Notifications\FailureToLogRequestForApproval;
use App\Notifications\ManpowerRequestApproved;
use App\Notifications\ManpowerRequestForApproval;
use App\Notifications\OvertimeRequestApproved;
use App\Notifications\OvertimeRequestForApproval;
use App\Notifications\PanRequestApproved;
use App\Notifications\PanRequestForApproval;
use App\Notifications\PayrollRequestApproved;
use App\Notifications\TravelRequestApproval;
use App\Notifications\TravelRequestApproved;
use App\Notifications\TravelRequestForApproval;
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
        $nextApproval = $model->getNextPendingApproval();
        if ($nextApproval) {
            $nextApprovalUser = $nextApproval["user_id"];
            switch ($modelType) {
                case ApprovalModels::LeaveEmployeeRequest->name:
                    Users::find($nextApprovalUser)->notify(new LeaveRequestForApproval($model)); // Notify the next Approval
                    break;
                case ApprovalModels::TravelOrder->name:
                    Users::find($nextApprovalUser)->notify(new TravelRequestForApproval($model)); // Notify the next Approval
                    break;
                case ApprovalModels::CashAdvance->name:
                    Users::find($nextApprovalUser)->notify(new CashAdvanceForApproval($model)); // Notify the next Approval
                    break;
                case ApprovalModels::FailureToLog->name:
                    Users::find($nextApprovalUser)->notify(new FailureToLogRequestForApproval($model)); // Notify the next Approval
                    break;
                case ApprovalModels::ManpowerRequest->name:
                    Users::find($nextApprovalUser)->notify(new ManpowerRequestForApproval($model)); // Notify the next Approval
                    break;
                case ApprovalModels::Overtime->name:
                    Users::find($nextApprovalUser)->notify(new OvertimeRequestForApproval($model)); // Notify the next Approval
                    break;
                case ApprovalModels::EmployeePanRequest->name:
                    Users::find($nextApprovalUser)->notify(new PanRequestForApproval($model)); // Notify the next Approval
                    break;
            }
        } else {
            switch ($modelType) {
                case ApprovalModels::LeaveEmployeeRequest->name:
                    Users::find($model->created_by)->notify(new LeaveRequestApproved($model)); // Notify the requestor
                    break;
                case ApprovalModels::TravelOrder->name:
                    Users::find($model->requested_by)->notify(new TravelRequestApproved($model)); // Notify the requestor
                    break;
                case ApprovalModels::CashAdvance->name:
                    Users::find($model->created_by)->notify(new CashAdvanceApproved($model)); // Notify the requestor
                    break;
                case ApprovalModels::FailureToLog->name:
                    Users::find($model->created_by)->notify(new FailureToLogRequestApproved($model)); // Notify the requestor
                    break;
                case ApprovalModels::ManpowerRequest->name:
                    Users::find($model->requested_by)->notify(new ManpowerRequestApproved($model)); // Notify the requestor
                    break;
                case ApprovalModels::Overtime->name:
                    Users::find($model->prepared_by)->notify(new OvertimeRequestApproved($model)); // Notify the requestor
                    break;
                case ApprovalModels::EmployeePanRequest->name:
                    Users::find($model->created_by)->notify(new PanRequestApproved($model)); // Notify the requestor
                    break;
            }
        }
        return new JsonResponse(["success" => $result["success"], "message" => $result['message']], $result["status_code"]);;
    }
}
