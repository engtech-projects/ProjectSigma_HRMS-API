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

    public function getAllByAuthUser()
    {
        $userId = auth()->user()->id;
        $result = $this->getAllManpowerRequest();
        $manpowerRequests = $result->map(function ($item) use ($userId) {
            $approvals = collect($item['approvals']);
            $nextPendingApproval = $approvals->where('status', ManpowerRequestStatus::PENDING)->first();
            $userApprovals = $approvals->where('user_id', $userId)
                ->where('status', ManpowerRequestStatus::PENDING);
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
