<?php

namespace App\Http\Services;

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
    public function getMyRequest()
    {
        return CashAdvance::with("employee", "department", "project")
            ->myRequests()
            ->get();
    }
    public function getMyApprovals()
    {
        return CashAdvance::with("employee", "department", "project")->myApprovals()->get();
    }
}
