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
        return TravelOrder::with(['department'])->get();
    }

    public function getMyRequest()
    {
        $manpowerRequest = $this->getAll();
        return $manpowerRequest->where('requested_by', auth()->user()->id)->load('user.employee');
    }

    public function getMyApprovals()
    {
        $userId = auth()->user()->id;
        $result = TravelOrder::with(['department'])
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
