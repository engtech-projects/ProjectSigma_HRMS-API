<?php

namespace App\Http\Services;

use App\Enums\RequestApprovalStatus;
use App\Models\ManpowerRequest;

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
        return $this->manpowerRequest->get();
    }

    public function getAllManpowerRequest()
    {
        $userId = auth()->user()->id;
        return ManpowerRequest::requestStatusPending()
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
        $result = $this->getAllManpowerRequest();
        return $result->filter(function ($item) use ($userId) {
            $nextPendingApproval = $item->getNextPendingApproval();
            return  ($nextPendingApproval && $userId === $nextPendingApproval['user_id']);
        });
    }

    public function createManpowerRequest(array $attributes)
    {
        $this->manpowerRequest->create($attributes);
    }

    public function update(array $attributes, ManpowerRequest $manpowerRequest)
    {
        if (array_key_exists('approvals', $attributes)) {
            $manpowerRequest->approvals = $this->updateApproval(json_decode($attributes['approvals'], true), $manpowerRequest);
        }
        return $manpowerRequest->update($attributes);
    }
    public function updateApproval($approval, $manpowerRequest)
    {
        $userApproval = $this->getUserPendingApproval(collect($manpowerRequest->approvals), auth()->user()->id)->first();
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
        return $manpowerRequest->approvals;
    }
}
