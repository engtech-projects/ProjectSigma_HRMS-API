<?php

namespace App\Http\Services;

use App\Models\ManpowerRequest;
use Illuminate\Support\Facades\DB;

class ManpowerServices
{
    protected $manpowerRequest;
    const REQUEST_BY_AUTH_USER = "manpower-request-by-auth-user";
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
    public function getAllByAuthUser()
    {
        $userId = auth()->user()->id;
        $result = ManpowerRequest::requestStatusPending()
            ->with(['user'])
            ->whereJsonLength('approvals', '>', 0)
            ->where(function ($query) use ($userId) {
                $query->whereJsonContains('approvals', ['user_id' => $userId]);
            })->get();
        $manpowerRequests = $result->map(function ($item) use ($userId) {
            $approvals = collect($item['approvals']);
            $deniedApproval = $approvals->where('status', 'Denied')->first();
            if ($deniedApproval) {
                $item->approvals = [];
            } else {
                $manpowerRequests = $approvals->where('user_id', $userId)
                    ->where('status', 'Pending');
                $item->approvals = $manpowerRequests;
            }
            return $item;
        })->reject(function ($item) {
            return empty($item['approvals']);
        });
        return $manpowerRequests;
    }
}
