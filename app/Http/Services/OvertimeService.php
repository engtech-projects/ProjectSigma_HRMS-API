<?php

namespace App\Http\Services;

use App\Enums\RequestApprovalStatus;
use App\Models\Overtime;

class OvertimeService
{
    protected $leaveRequest;
    public function __construct(Overtime $leaveRequest)
    {
        $this->leaveRequest = $leaveRequest;
    }

    public function getAll()
    {
        return Overtime::with("employees")->orderBy('created_at', 'desc')->get();
    }

    public function create($attributes)
    {
        return Overtime::create($attributes);
    }

    public function getMyRequests()
    {
        return Overtime::with(['employees', 'department', 'project'])
            ->where("created_by", auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getMyLeaveForm()
    {
        $userId = auth()->user()->id;
        return Overtime::requestStatusPending()
            ->with(['employees', 'department'])
            ->whereJsonLength('approvals', '>', 0)
            ->whereJsonContains('approvals', ['user_id' => $userId])
            ->get();
    }

    public function getAllLeaveRequest()
    {
        $userId = auth()->user()->id;
        return Overtime::requestStatusPending()
            ->with(['user.employee'])
            ->whereJsonLength('approvals', '>', 0)
            ->whereJsonContains('approvals', ['user_id' => $userId, 'status' => RequestApprovalStatus::PENDING])
            ->get();
    }

    public function getMyRequest()
    {
        $manpowerRequest = $this->getAll();
        return $manpowerRequest->where('created_by', auth()->user()->id)->load('user.employee');
    }

    public function getMyApprovals()
    {
        return Overtime::with(['employees', 'department', 'project'])
            ->myApprovals()
            ->get();
    }
}
