<?php

namespace App\Http\Services;

use App\Http\Resources\EmployeeLeaveResource;
use App\Models\EmployeeLeaves;

class EmployeeLeaveService
{
    protected $leaveRequest;
    public function __construct(EmployeeLeaves $leaveRequest)
    {
        $this->leaveRequest = $leaveRequest;
    }

    public function getAll()
    {
        return EmployeeLeaves::with(['employee', 'department', 'project', 'leave'])->get();
    }

    public function create($attributes)
    {
        return EmployeeLeaves::create($attributes);
    }

    public function getMyRequests()
    {
        return EmployeeLeaves::with(['employee', 'department', 'project'])
            ->where("created_by", auth()->user()->id)
            ->get();
    }

    public function getMyLeaveForm()
    {
        $userId = auth()->user()->id;
        return EmployeeLeaves::requestStatusPending()
            ->with(['employee', 'department'])
            ->whereJsonLength('approvals', '>', 0)
            ->whereJsonContains('approvals', ['user_id' => $userId])
            ->get();
    }

    public function getMyRequest()
    {
        $leaveRequest = EmployeeLeaves::myRequests()
        ->orderBy('created_by', 'DESC')
        ->paginate(config("app.pagination_per_page", 10));
        return EmployeeLeaveResource::collection($leaveRequest);
    }

    public function getMyApprovals()
    {
        return EmployeeLeaves::with(['employee', 'department', 'project'])->myApprovals()->get();
    }
}
