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
        $manpowerRequest = $this->getAll();
        return $manpowerRequest->where('requested_by', auth()->user()->id)->load('user.employee');
    }

    public function getMyApprovals()
    {
        $userId = auth()->user()->id;
        $result = TravelOrder::with(['employee', 'department'])
            ->requestStatusPending()
            ->authUserPending()
            ->get();
        return $result->filter(function ($item) use ($userId) {
            $nextPendingApproval = $item->getNextPendingApproval();
            return ($nextPendingApproval && $userId === $nextPendingApproval['user_id']);
        });
    }

    public function updateApproval($approval, $leaveRequest)
    {
        $userApproval = $this->getUserPendingApproval(collect($leaveRequest->approvals), auth()->user()->id)->first();
        if ($userApproval) {
            $approvalToUpdate = collect($leaveRequest->approvals)->search($userApproval);
            $leaveRequestApproval = collect($leaveRequest->approvals)->map(function ($item, int $key) use ($approvalToUpdate, $approval) {
                $approval = collect($approval)->first();
                if ($key === $approvalToUpdate) {
                    $item['status'] = $approval['status'];
                }
                return $item;
            });
            $leaveRequest->approvals = $leaveRequestApproval;
        }
        return $leaveRequest->approvals;
    }
}
