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
        return Overtime::with(['employee', 'department', 'project'])->get();
    }

    public function create($attributes)
    {
        return Overtime::create($attributes);
    }

    public function getMyRequests()
    {
        return Overtime::with(['employee', 'department', 'project'])
            ->where("created_by", auth()->user()->id)
            ->get();
    }

    public function getMyLeaveForm()
    {
        $userId = auth()->user()->id;
        return Overtime::requestStatusPending()
            ->with(['employee', 'department'])
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
        return $manpowerRequest->where('prepared_by', auth()->user()->id)->load('user.employee');
    }

    public function getMyApprovals()
    {
        $userId = auth()->user()->id;
        $result = Overtime::with(['employee', 'department', 'project'])
            ->requestStatusPending()
            ->authUserPending()
            ->get();
        return $result->filter(function ($item) use ($userId) {
            $nextPendingApproval = $item->getNextPendingApproval();
            return ($nextPendingApproval && $userId === $nextPendingApproval['user_id']);
        });
    }
}
