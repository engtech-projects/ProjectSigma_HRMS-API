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
use App\Notifications\TravelRequestApproved;
use App\Notifications\TravelRequestForApproval;
use Illuminate\Http\JsonResponse;
use App\Enums\RequestApprovalStatus;
use App\Http\Controllers\Controller;
use App\Models\Users;
use App\Notifications\AllowanceRequestApproved;
use App\Notifications\AllowanceRequestForApproval;
use App\Notifications\LeaveRequestApproved;
use App\Notifications\LeaveRequestForApproval;
use App\Notifications\PayrollRequestApproved;
use App\Notifications\PayrollRequestForApproval;
use Carbon\Carbon;

class ApproveApproval extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($modelType, $model)
    {
        $result = $model->updateApproval(['status' => RequestApprovalStatus::APPROVED, "date_approved" => Carbon::now()]);
        $nextApproval = $model->getNextPendingApproval();
        if ($nextApproval) {
            $nextApprovalUser = $nextApproval["user_id"];
            $notificationMap = [
                ApprovalModels::LeaveEmployeeRequest->name => LeaveRequestForApproval::class,
                ApprovalModels::TravelOrder->name => TravelRequestForApproval::class,
                ApprovalModels::CashAdvance->name => CashAdvanceForApproval::class,
                ApprovalModels::FailureToLog->name => FailureToLogRequestForApproval::class,
                ApprovalModels::ManpowerRequest->name => ManpowerRequestForApproval::class,
                ApprovalModels::Overtime->name => OvertimeRequestForApproval::class,
                ApprovalModels::EmployeePanRequest->name => PanRequestForApproval::class,
                ApprovalModels::GenerateAllowance->name => AllowanceRequestForApproval::class,
                ApprovalModels::GeneratePayroll->name => PayrollRequestForApproval::class,
            ];
            if (isset($notificationMap[$modelType])) {
                Users::find($nextApprovalUser)->notify(new $notificationMap[$modelType]($model));
            }
        } else {
            $notificationMap = [
                ApprovalModels::LeaveEmployeeRequest->name => LeaveRequestApproved::class,
                ApprovalModels::TravelOrder->name => TravelRequestApproved::class,
                ApprovalModels::CashAdvance->name => CashAdvanceApproved::class,
                ApprovalModels::FailureToLog->name => FailureToLogRequestApproved::class,
                ApprovalModels::ManpowerRequest->name => ManpowerRequestApproved::class,
                ApprovalModels::Overtime->name => OvertimeRequestApproved::class,
                ApprovalModels::EmployeePanRequest->name => PanRequestApproved::class,
                ApprovalModels::GenerateAllowance->name => AllowanceRequestApproved::class,
                ApprovalModels::GeneratePayroll->name => PayrollRequestApproved::class,
            ];
            if (isset($notificationMap[$modelType])) {
                Users::find($model->created_by)->notify(new $notificationMap[$modelType]($model));
            }
        }
        return new JsonResponse(["success" => $result["success"], "message" => $result['message']], $result["status_code"]);
    }
}
