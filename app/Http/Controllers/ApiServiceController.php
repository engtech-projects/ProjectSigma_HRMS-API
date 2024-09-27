<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormatApprovalsRequest;
use App\Http\Resources\HrmsServiceApprovalAttributeResource;

class ApiServiceController extends Controller
{
    public function formatApprovals(FormatApprovalsRequest $request) {
        $validatedData = $request->validated();
        return response()->json([
            "message" => "Successfully Formatted Approvals.",
            "data" => new HrmsServiceApprovalAttributeResource($validatedData),
            "success" => true,
        ]);
    }
    public function formatSingleApproval(FormatApprovalsRequest $request) {
        $validatedData = $request->validated();
        return response()->json([
            "message" => "Successfully Formatted Approvals.",
            "data" => new HrmsServiceApprovalAttributeResource($validatedData),
            "success" => true,
        ]);
    }
}
