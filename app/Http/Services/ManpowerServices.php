<?php

namespace App\Http\Services;

use App\Models\ManpowerRequest;
use Illuminate\Support\Facades\DB;

class ManpowerServices
{
    protected $manpowerRequest;
    const REQUEST_BY_AUTH_USER = "manpower-approval-for-user";
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
        return $this->manpowerRequest->all();
    }
    public function getAllManpowerRequest()
    {
        $userId = auth()->user()->id;
        return ManpowerRequest::requestStatusPending()
            ->with(['user'])
            ->whereJsonLength('approvals', '>', 0)
            ->where(function ($query) use ($userId) {
                $query->whereJsonContains('approvals', ['user_id' => $userId, 'status' => 'Pending']);
            })->get();
    }

    public function getAllByAuthUser()
    {
        $userId = auth()->user()->id;
        $result = $this->getAllManpowerRequest();
        $manpowerRequests = $result->map(function ($item) use ($userId) {
            $approvals = collect($item['approvals']);
            $nextPendingApproval = $approvals->where('status', 'Pending')->first();
            $userApprovals = $approvals->where('user_id', $userId)
                ->where('status', 'Pending');
            $nextUserApproval = $userApprovals->first();
            /* dd([
                "approvals" => $approvals,
                "user_approvals" => $userApprovals,
                "next_user_approval" => $nextPendingApproval
            ]); */
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
