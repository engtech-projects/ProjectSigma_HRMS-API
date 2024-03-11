<?php

namespace App\Http\Services;

use App\Models\ManpowerRequest;
use Illuminate\Support\Facades\DB;

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
        /* $manpowerRequests = ManpowerRequest::with(['user'])->whereJsonContains('approvals', ['user_id' => auth()->user()->id])->get(); */
        $userId = auth()->user()->id;
        $result = ManpowerRequest::with(['user'])
            ->whereJsonLength('approvals', '>', 0)
            ->where(function ($query) use ($userId) {
                $query->whereJsonContains('approvals', ['user_id' => $userId])
                    ->orWhereJsonContains('approvals', ['user_id' => strval($userId)]);
            })
            ->get();

        $manpowerRequests = $result->map(function ($item) use ($userId) {
            $approvals = collect(json_decode($item['approvals']));
            $manpowerRequests = $approvals->where('user_id', $userId)->whereIn('status', ['Pending', 'Approved'])->toArray();
            $item->approvals = $manpowerRequests;
            return $item;
        })->reject(function ($item) {
            return empty($item['approvals']);
        });

        return $manpowerRequests;
    }
}
