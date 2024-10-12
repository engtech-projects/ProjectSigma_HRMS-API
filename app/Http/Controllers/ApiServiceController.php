<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormatApprovalsRequest;
use App\Http\Requests\FormatSingleApprovalRequest;
use App\Http\Requests\FormatUserEmployeesRequest;
use App\Http\Resources\HrmsServiceApprovalAttributeResource;
use App\Http\Resources\UserEmployeeSummaryResource;
use App\Models\Users;

class ApiServiceController extends Controller
{
    public function formatApprovals(FormatApprovalsRequest $request)
    {
        $validatedData = $request->validated();
        return response()->json([
            "message" => "Successfully Formatted Approvals.",
            "data" => HrmsServiceApprovalAttributeResource::collection($validatedData['approvals']),
            "success" => true,
        ]);
    }
    public function formatSingleApproval(FormatSingleApprovalRequest $request)
    {
        $validatedData = $request->validated();
        return response()->json([
            "message" => "Successfully Formatted Approvals.",
            "data" => new HrmsServiceApprovalAttributeResource($validatedData),
            "success" => true,
        ]);
    }
    public function getUserEmployees(FormatUserEmployeesRequest $request)
    {
        $validatedData = $request->validated();
        $users = Users::whereIn('id', $validatedData['user_ids'])->get();
        return response()->json([
            "message" => "Successfully fetched users.",
            "data" => UserEmployeeSummaryResource::collection($users),
            "success" => true,
        ]);
    }
}
