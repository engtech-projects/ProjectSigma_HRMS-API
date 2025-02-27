<?php

namespace App\Http\Controllers\Actions;

use App\Enums\ApprovalModels;
use App\Notifications\CashAdvanceDenied;
use App\Notifications\FailureToLogRequestDenied;
use App\Notifications\ManpowerRequestDenied;
use App\Notifications\OvertimeRequestDenied;
use App\Notifications\PanRequestDenied;
use App\Notifications\TravelRequestDenied;
use Illuminate\Http\JsonResponse;
use App\Enums\RequestApprovalStatus;
use App\Enums\RequestStatuses;
use App\Enums\RequestStatusType;
use App\Http\Controllers\Controller;
use App\Http\Requests\VoidRequest;
use App\Models\Approvals;
use App\Models\RequestVoid;
use App\Models\Users;
use App\Notifications\AllowanceRequestDenied;
use App\Notifications\LeaveRequestDenied;
use App\Notifications\PayrollRequestDenied;
use App\Notifications\VoidRequestForApproval;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class VoidRequestAction extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($modelType, $model, VoidRequest $request)
    {
        $cacheKey = "void" . $modelType . $model->id . '-'. Auth::user()->id;
        if (Cache::has($cacheKey)) {
            return new JsonResponse(["success" => false, "message" => "Too Many Attempts"], 429);
        }
        return Cache::remember($cacheKey, 5, function () use ($modelType, $model, $request) {
            return $this->createVoid($modelType, $model, $request);
        });

    }


    public function createVoid($modelType, $model, VoidRequest $request)
    {
        if ($model->request_status != RequestStatuses::APPROVED) {
            return new JsonResponse(["success" => false, "message" => "Cannot Void Request that is not Approved."], JsonResponse::HTTP_BAD_REQUEST);
        }
        $attribute = $request->validated();
        $approvals = Approvals::where("form", "Void Requests")->first();
        $approvalModels = ApprovalModels::toArray();
        $attribute["request_type"] = $approvalModels[$modelType];
        $attribute["request_id"] = $model->id;
        $attribute["approvals"] = $approvals;
        $attribute["created_by"] = Auth::user()->id;
        $createData = RequestVoid::create($attribute);
        if (!$createData) {
            return new JsonResponse(["success" => false, "message" => "Failed to save Void Request."], JsonResponse::HTTP_BAD_REQUEST);
        }
        if ($model->getNextPendingApproval()) {
            Users::find($model->getNextPendingApproval()['user_id'])->notify(new VoidRequestForApproval($model));
        }
        return new JsonResponse(["success" => true, "message" => "Successfully submitted Void Request."], JsonResponse::HTTP_CREATED);
    }
}
