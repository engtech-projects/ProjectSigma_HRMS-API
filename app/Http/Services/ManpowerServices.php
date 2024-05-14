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
        return $this->manpowerRequest->with(["position"])->get();
    }
    public function getAllForHiring()
    {
        return $this->manpowerRequest->forHiring()->get();
    }

    public function getAllManpowerRequest()
    {
        $userId = auth()->user()->id;
        return ManpowerRequest::requestStatusPending()
            ->with(['user.employee', "position"])
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
}
