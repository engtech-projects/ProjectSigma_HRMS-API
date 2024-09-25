<?php

namespace App\Http\Services;

use App\Enums\RequestApprovalStatus;
use App\Models\TravelOrder;

class EmployeeTravelOrderService
{
    protected $leaveRequest;
    public function __construct(TravelOrder $travelOrder)
    {
        $this->leaveRequest = $travelOrder;
    }
    public function getAll()
    {
        return TravelOrder::with(['employee', 'department'])->get();
    }
    public function create($attributes)
    {
        return TravelOrder::create($attributes);
    }
    public function getMyRequests()
    {
        return TravelOrder::with(['employee', 'department'])
            ->where("created_by", auth()->user()->id)
            ->get();
    }
    public function getAllLeaveRequest()
    {
        $userId = auth()->user()->id;
        return TravelOrder::requestStatusPending()
            ->with(['user.employee'])
            ->whereJsonLength('approvals', '>', 0)
            ->whereJsonContains('approvals', ['user_id' => $userId, 'status' => RequestApprovalStatus::PENDING])
            ->get();
    }
    public function getMyRequest()
    {
        return TravelOrder::with('user.employee')->myRequests()->get();
    }
    public function getMyApprovals()
    {
        return TravelOrder::with(['employee', 'department'])
            ->myApprovals()
            ->get();
    }
}
