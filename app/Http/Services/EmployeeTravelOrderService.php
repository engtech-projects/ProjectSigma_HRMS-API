<?php

namespace App\Http\Services;

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
