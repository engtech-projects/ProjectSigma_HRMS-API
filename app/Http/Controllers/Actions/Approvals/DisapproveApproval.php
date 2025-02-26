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
use App\Notifications\AllowanceRequestDenied;
use App\Notifications\LeaveRequestDenied;
use App\Notifications\PayrollRequestDenied;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DisapproveApproval extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($modelType, $model, DisapproveApprovalRequest $request)
    {
        $cacheKey = "disapprove" . $modelType . $model->id . '-'. Auth::user()->id;
        if (Cache::has($cacheKey)) {
            return new JsonResponse(["success" => false, "message" => "Too Many Attempts"], 429);
        }
        return Cache::remember($cacheKey, 5, function () use ($modelType, $model, $request) {
            return $this->disapprove($modelType, $model, $request);
        });

    }
    public function disapprove($modelType, $model, DisapproveApprovalRequest $request)
    {
        $attribute = $request->validated();
        $result = collect($model->updateApproval(['status' => RequestApprovalStatus::DENIED, 'remarks' => $attribute['remarks'], "date_denied" => Carbon::now()]));
        $notificationMap = [
            ApprovalModels::LeaveEmployeeRequest->name => LeaveRequestDenied::class,
            ApprovalModels::TravelOrder->name => TravelRequestDenied::class,
            ApprovalModels::CashAdvance->name => CashAdvanceDenied::class,
            ApprovalModels::FailureToLog->name => FailureToLogRequestDenied::class,
            ApprovalModels::ManpowerRequest->name => ManpowerRequestDenied::class,
            ApprovalModels::Overtime->name => OvertimeRequestDenied::class,
            ApprovalModels::EmployeePanRequest->name => PanRequestDenied::class,
            ApprovalModels::GenerateAllowance->name => AllowanceRequestDenied::class,
            ApprovalModels::GeneratePayroll->name => PayrollRequestDenied::class,
        ];
        if (isset($notificationMap[$modelType])) {
            Users::find($model->created_by)->notify(new $notificationMap[$modelType]($model));
        }
        return new JsonResponse(["success" => $result["success"], "message" => $result['message']], JsonResponse::HTTP_OK);
    }
}
