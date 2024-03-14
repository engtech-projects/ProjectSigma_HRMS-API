<?php

namespace App\Http\Services;

use App\Enums\ManpowerRequestStatus;
use App\Models\ManpowerRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;

class ManpowerServices
{
    protected $manpowerRequest;
    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct(ManpowerRequest $manpowerRequest)
    {
        $this->manpowerRequest = $manpowerRequest;
    }
    public function getAll()
    {
        return $this->manpowerRequest->simplePaginate(15);
    }
    public function getAllManpowerRequest()
    {
        $userId = auth()->user()->id;
        return ManpowerRequest::requestStatusPending()
            ->with(['user'])
            ->whereJsonLength('approvals', '>', 0)
            ->where(function ($query) use ($userId) {
                $query->whereJsonContains('approvals', ['user_id' => $userId, 'status' => ManpowerRequestStatus::PENDING]);
            })->get();
    }

    public function update($attributes, ManpowerRequest $manpowerRequest)
    {
        $toUpdate = request()->get('to_update');
        if ($toUpdate === 'approval_status') {
            $this->updateApproval(json_decode($attributes['approvals'], true), $manpowerRequest);
        }
        return $manpowerRequest->update($attributes);
    }
    public function updateApproval($approval, $manpowerRequest)
    {
        $userApproval = $this->getUserApprovals(collect($manpowerRequest->approvals), auth()->user()->id)->first();
        if ($userApproval) {
            $approvalToUpdate = collect($manpowerRequest->approvals)->search($userApproval);
            $manpowerRequestApproval = collect($manpowerRequest->approvals)->map(function ($item, int $key) use ($approvalToUpdate, $approval) {
                $approval = collect($approval)->first();
                if ($key === $approvalToUpdate) {
                    $item['status'] = $approval['status'];
                }
                return $item;
            });
            $manpowerRequest->approvals = $manpowerRequestApproval;
        }
        $manpowerRequest->save();
    }
    public function updateManpowerRequest()
    {
    }

    public function getUserApprovals($approvals, $userId)
    {
        return $approvals->where('user_id', $userId)
            ->where('status', ManpowerRequestStatus::PENDING);
    }

    public function getNextPendingApproval($approvals, $userId)
    {
        return $approvals->where('status', ManpowerRequestStatus::PENDING)->first();
    }

    public function getAllByAuthUser()
    {
        $userId = auth()->user()->id;
        $result = $this->getAllManpowerRequest();
        $manpowerRequests = $result->map(function ($item) use ($userId) {
            $approvals = collect($item['approvals']);
            $nextPendingApproval = $this->getNextPendingApproval($approvals, $userId);
            $userApprovals = $this->getUserApprovals($approvals, $userId);
            $nextUserApproval = $userApprovals->first();
            $item->approvals = $userApprovals;
            if ($nextUserApproval && $userId != $nextPendingApproval['user_id']) {
                $item['approvals'] = [];
            }
            if (!$userApprovals) {
                $item->approvals = [];
            }
            return $item;
        })->reject(function ($item) {
            return empty($item['approvals']);
        });
        return $manpowerRequests;
    }
}
