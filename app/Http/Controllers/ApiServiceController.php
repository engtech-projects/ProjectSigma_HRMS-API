<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormatApprovalsRequest;
use App\Http\Resources\HrmsServiceApprovalResource;

class ApiServiceController extends Controller
{
    public function formatApprovals(FormatApprovalsRequest $request) {
        $validatedData = $request->validated();
        return response()->json([
            "message" => "Successfully Formatted Approvals.",
            "data" => new HrmsServiceApprovalResource($validatedData),
            "success" => true,
        ]);
    }
}
