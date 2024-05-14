<?php

namespace App\Http\Services;

use App\Enums\RequestApprovalStatus;
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

    public function getAllLeaveRequest()
    {
        $userId = auth()->user()->id;
        return EmployeeLeaves::requestStatusPending()
            ->with(['user.employee'])
            ->whereJsonLength('approvals', '>', 0)
            ->whereJsonContains('approvals', ['user_id' => $userId, 'status' => RequestApprovalStatus::PENDING])
            ->get();
    }

    public function getMyRequest()
    {
        $manpowerRequest = $this->getAll();
        return $manpowerRequest->where('requested_by', auth()->user()->id)->load('user.employee');
    }

    public function getMyApprovals()
    {
        $userId = auth()->user()->id;
        $result = EmployeeLeaves::with(['employee', 'department', 'project'])
            ->requestStatusPending()
            ->authUserPending()
            ->get();
        return $result->filter(function ($item) use ($userId) {
            $nextPendingApproval = $item->getNextPendingApproval();
            return ($nextPendingApproval && $userId === $nextPendingApproval['user_id']);
        });
    }
}
