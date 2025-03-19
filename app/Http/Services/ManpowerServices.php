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
        return $this->manpowerRequest->with("position", "user.employee")->orderBy('created_at', 'desc')->paginate();
    }
    public function getAllForHiring()
    {
        return $this->manpowerRequest->with('job_applicants', 'user.employee')->forHiring()->orderBy('created_at', 'DESC')->paginate();
    }
    public function getAllManpowerRequest()
    {
        $userId = auth()->user()->id;
        return ManpowerRequest::requestStatusPending()
            ->with(['user.employee', "position"])
            ->orderBy('created_at', 'desc')
            ->get();
    }
    public function getMyRequest()
    {
        return ManpowerRequest::myRequests()->with('user.employee')->paginate();
    }
    public function getMyApprovals()
    {
        $userId = auth()->user()->id;
        return ManpowerRequest::requestStatusPending()
            ->with(['user.employee', "position"])
            ->orderBy('created_at', 'desc')->myApprovals()->paginate();
    }
    public function createManpowerRequest(array $attributes)
    {

        $main = $this->manpowerRequest->fill($attributes);
        $main->job_description_attachment = $this->uploadFile($attributes['job_description_attachment'], ManpowerRequest::JDA_DIR);

        if($main->save()){
            if ($main->getNextPendingApproval()) {
                Users::find($main->getNextPendingApproval()['user_id'])->notify(new ManpowerRequestForApproval($main));
            }
            return true;
        }
        return false;
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
        return $this->manpowerRequest->with("position", "user.employee")->where('fill_status', FillStatuses::OPEN->value)->orderBy('created_at', 'desc')->paginate();
    }
    public function getFilledPositions()
    {
        return $this->manpowerRequest->with("position", "user.employee")->where('fill_status', FillStatuses::FILLED->value)->orderBy('created_at', 'desc')->paginate();
    }
    public function getOnHoldPositions()
    {
        return $this->manpowerRequest->with("position", "user.employee")->where('fill_status', FillStatuses::HOLD->value)->orderBy('created_at', 'desc')->paginate();
    }
}
