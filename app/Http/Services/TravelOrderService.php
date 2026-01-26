<?php

namespace App\Http\Services;

use App\Models\TravelOrder;

class TravelOrderService
{
    protected $leaveRequest;
    public function __construct(TravelOrder $leaveRequest)
    {
        $this->leaveRequest = $leaveRequest;
    }
    public function getAll()
    {
        return TravelOrder::with(['department',"employees"])->orderBy("id", "desc")->get();
    }
    public function getMyRequest()
    {
        return TravelOrder::with('user.employee')->myRequests()->get();
    }
    public function getMyApprovals()
    {
        $userId = auth()->user()->id;
        $result = TravelOrder::with(['department',"employees"])
            ->requestStatusPending()
            ->authUserPending()
            ->get();
        return $result->filter(function ($item) use ($userId) {
            $nextPendingApproval = $item->getNextPendingApproval();
            return ($nextPendingApproval && $userId === $nextPendingApproval['user_id']);
        });
    }
}
