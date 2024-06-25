<?php

namespace App\Http\Controllers\Actions\Approvals;

use App\Enums\ApprovalModels;
use App\Notifications\CashAdvanceDenied;
use App\Notifications\FailureToLogRequestDenied;
use App\Notifications\ManpowerRequestDenied;
use App\Notifications\OvertimeRequestDenied;
use App\Notifications\PanRequestDenied;
use App\Notifications\TravelRequestDenied;
use Illuminate\Http\JsonResponse;
use App\Enums\RequestApprovalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\DisapproveApprovalRequest;
use App\Models\Users;
use App\Notifications\LeaveRequestDenied;
use App\Notifications\LeaveRequestForApproval;

class DisapproveApproval extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($modelType, $model, DisapproveApprovalRequest $request)
    {
        $attribute = $request->validated();
        $result = collect($model->updateApproval(['status' => RequestApprovalStatus::DENIED, 'remarks' => $attribute['remarks']]));
        switch ($modelType) {
            case ApprovalModels::LeaveEmployeeRequest->name:
                Users::find(1)->notify(new LeaveRequestDenied($model)); // Notify Request Creator Request DENIED (leave & cashadvance)
                break;
            case ApprovalModels::TravelOrder->name:
                    Users::find($model->requested_by)->notify(new TravelRequestDenied($model)); // Notify Request Creator Request DENIED
                break;
            case ApprovalModels::CashAdvance->name:
                    Users::find(1)->notify(new CashAdvanceDenied($model)); // Notify Request Creator Request DENIED
                break;
            case ApprovalModels::FailureToLog->name:
                    Users::find($model->requested_by)->notify(new FailureToLogRequestDenied($model)); // Notify Request Creator Request DENIED
                break;
            case ApprovalModels::ManpowerRequest->name:
                    Users::find($model->requested_by)->notify(new ManpowerRequestDenied($model)); // Notify Request Creator Request DENIED
                break;
            case ApprovalModels::Overtime->name:
                    Users::find($model->requested_by)->notify(new OvertimeRequestDenied($model)); // Notify Request Creator Request DENIED
                break;
            case ApprovalModels::EmployeePanRequest->name:
                    Users::find($model->requested_by)->notify(new PanRequestDenied($model)); // Notify Request Creator Request DENIED
                break;
            default:
                break;
        }
        return new JsonResponse(["success" => $result["success"], "message" => $result['message']], JsonResponse::HTTP_OK);
    }
}
