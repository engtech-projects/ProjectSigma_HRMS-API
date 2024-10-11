<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormatApprovalsRequest;
use App\Http\Requests\FormatSingleApprovalRequest;
use App\Http\Requests\FormatUserEmployeesRequest;
use App\Http\Resources\HrmsServiceApprovalAttributeResource;
use App\Http\Resources\UserEmployeeResource;
use App\Models\User;

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
    public function getUserEmployees(FormatUserEmployeesRequest $request, User $user)
    {
        $validatedData = $request->validated();
        // $user = User::find($validatedData['user_id']);
        return response()->json([
            "message" => "Successfully fetched users.",
            "data" => new UserEmployeeResource($user),
            "success" => true,
        ]);
    }
}
