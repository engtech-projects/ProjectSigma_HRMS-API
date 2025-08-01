<?php

namespace App\Http\Controllers;

use App\Enums\UserTypes;
use App\Http\Requests\FormatApprovalsRequest;
use App\Http\Requests\FormatSingleApprovalRequest;
use App\Http\Requests\FormatUserEmployeesRequest;
use App\Http\Resources\EmployeeDetailedEnumResource;
use App\Http\Resources\HrmsServiceApprovalAttributeResource;
use App\Http\Resources\SyncListDepartmentResource;
use App\Http\Resources\SyncListEmployeeResource;
use App\Http\Resources\SyncListUserResource;
use App\Http\Resources\UserEmployeeSummaryResource;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Users;
use Illuminate\Http\JsonResponse;

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
    public function getEmployeeList()
    {
        $employeeList = Employee::withTrashed()
        ->orderBy('family_name')
        ->get();
        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => SyncListEmployeeResource::collection($employeeList),
        ]);
    }
    public function getDepartmentList()
    {
        $main = Department::withTrashed()
        ->get();
        return response()->json([
            "message" => "Successfully fetched users.",
            "data" => SyncListDepartmentResource::collection($main),
            "success" => true,
        ]);
    }
    public function getUserList()
    {
        $users = Users::withTrashed()
        ->with("employee")
        ->get();
        return response()->json([
            "message" => "Successfully fetched users.",
            "data" => SyncListUserResource::collection($users),
            "success" => true,
        ]);
    }
}
