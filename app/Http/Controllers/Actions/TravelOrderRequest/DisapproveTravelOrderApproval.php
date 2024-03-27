<?php

namespace App\Http\Controllers\Actions\TravelOrderRequest;

use App\Models\TravelOrder;
use Illuminate\Http\JsonResponse;
use App\Enums\RequestApprovalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\DenyManpowerApprovalRequest;

class DisapproveTravelOrderApproval extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DenyManpowerApprovalRequest $request, TravelOrder $travelOrder)
    {
        $attribute = $request->validated();
        $result = collect($travelOrder->updateApproval(['status' => RequestApprovalStatus::DENIED, 'remarks' => $attribute['remarks']]));
        return new JsonResponse(["success" => $result["success"], "message" => $result['message']], JsonResponse::HTTP_OK);
    }
}
