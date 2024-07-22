<?php

namespace App\Http\Services;

use App\Enums\RequestApprovalStatus;
use App\Models\CashAdvance;

class CashAdvanceService
{
    protected $leaveRequest;
    public function __construct(CashAdvance $leaveRequest)
    {
        $this->leaveRequest = $leaveRequest;
    }

    public function getAll()
    {
        return CashAdvance::with("employee", "department", "project", "cashAdvancePayments")->orderBy("created_at", "DESC")->get();
    }

    public function create($attributes)
    {
        return CashAdvance::create($attributes);
    }

    public function getMyRequests()
    {
        return CashAdvance::with(['employee', 'department'])
            ->where("created_by", auth()->user()->id)
            ->get();
    }

    public function getMyLeaveForm()
    {
        $userId = auth()->user()->id;
        return CashAdvance::requestStatusPending()
            ->with(['employee', 'department'])
            ->whereJsonLength('approvals', '>', 0)
            ->whereJsonContains('approvals', ['user_id' => $userId])
            ->get();
    }

    public function getAllLeaveRequest()
    {
        $userId = auth()->user()->id;
        return CashAdvance::requestStatusPending()
            ->with(['user.employee'])
            ->whereJsonLength('approvals', '>', 0)
            ->whereJsonContains('approvals', ['user_id' => $userId, 'status' => RequestApprovalStatus::PENDING])
            ->get();
    }

    public function getMyRequest()
    {
        $manpowerRequest = CashAdvance::with("employee", "department", "project")
            ->requestStatusPending()
            ->authUserPending()
            ->where('created_by', auth()->user()->id)
            ->get();
        return $manpowerRequest;
    }

    public function getMyApprovals()
    {
        $userId = auth()->user()->id;
        $result = CashAdvance::with("employee", "department", "project")
            ->requestStatusPending()
            ->authUserPending()
            ->get();
        return $result->filter(function ($item) use ($userId) {
            $nextPendingApproval = $item->getNextPendingApproval();
            return ($nextPendingApproval && $userId === $nextPendingApproval['user_id']);
        });
    }
}
