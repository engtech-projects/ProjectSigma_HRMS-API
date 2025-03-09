<?php

namespace App\Http\Services;

use App\Enums\RequestApprovalStatus;
use App\Http\Traits\UploadFileTrait;
use App\Models\ManpowerRequest;
use App\Models\Users;
use App\Notifications\ManpowerRequestForApproval;
use App\Enums\FillStatuses;

class ManpowerServices
{
    use UploadFileTrait;
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
        return $this->manpowerRequest->with(["position"])->orderBy('created_at', 'desc')->get();
    }
    public function getAllForHiring()
    {
        return $this->manpowerRequest->forHiring()->orderBy('created_at', 'DESC')->get();
    }
    public function getAllManpowerRequest()
    {
        $userId = auth()->user()->id;
        return ManpowerRequest::requestStatusPending()
            ->with(['user.employee', "position"])
            ->whereJsonLength('approvals', '>', 0)
            ->whereJsonContains('approvals', ['user_id' => $userId, 'status' => RequestApprovalStatus::PENDING])
            ->orderBy('created_at', 'desc')
            ->get();
    }
    public function getMyRequest()
    {
        return ManpowerRequest::with('user.employee')->myRequests()->get();
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
        $main = $this->manpowerRequest->fill($attributes);
        $main->job_description_attachment = $this->uploadFile($attributes['job_description_attachment'], ManpowerRequest::JDA_DIR);
        $main->save();
        if ($main->getNextPendingApproval()) {
            Users::find($main->getNextPendingApproval()['user_id'])->notify(new ManpowerRequestForApproval($main));
        }
    }
    public function update($request, $query)
    {
        $query->fill($request);
        if ($query->save()) {
            return $query;
        }
    }
    public function getOpenPositions()
    {
        return $this->manpowerRequest->with(["position"])->where('fill_status', FillStatuses::OPEN->value)->orderBy('created_at', 'desc')->get();
    }
    public function getFilledPositions()
    {
        return $this->manpowerRequest->with(["position"])->where('fill_status', FillStatuses::FILLED->value)->orderBy('created_at', 'desc')->get();
    }
    public function getOnHoldPositions()
    {
        return $this->manpowerRequest->with(["position"])->where('fill_status', FillStatuses::HOLD->value)->orderBy('created_at', 'desc')->get();
    }
}
