<?php

namespace App\Http\Services;

use App\Http\Traits\UploadFileTrait;
use App\Models\ManpowerRequest;
use App\Models\Users;
use App\Notifications\ManpowerRequestForApproval;
use App\Enums\FillStatuses;
use App\Enums\RequestStatuses;

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
    public function getAll($filter = [])
    {
        return $this->manpowerRequest
        ->with("position", "user.employee")
        ->when(isset($filter["date_required"]) && $filter["date_required"], function ($query) use ($filter) {
            $query->where('date_required', $filter["date_required"]);
        })
        ->when(isset($filter["date_requested"]) && $filter["date_requested"], function ($query) use ($filter) {
            $query->where('date_requested', $filter["date_requested"]);
        })
        ->when(isset($filter["position_id"]), function ($query) use ($filter) {
            $query->where('position_id', $filter["position_id"]);
        })
        ->when(isset($filter["fill_status"]), function ($query) use ($filter) {
            $query->where('fill_status', $filter["fill_status"]);
        })
        ->orderBy('created_at', 'desc')
        ->paginate();
    }
    public function getAllForHiring($filter = [])
    {
        return $this->manpowerRequest
            ->with('job_applicants', 'user.employee')
            ->forHiring()
            ->when(isset($filter["date_required"]) && $filter["date_required"], function ($query) use ($filter) {
                $query->where('date_required', $filter["date_required"]);
            })
            ->when(isset($filter["date_requested"]) && $filter["date_requested"], function ($query) use ($filter) {
                $query->where('date_requested', $filter["date_requested"]);
            })
            ->when(isset($filter["position_id"]), function ($query) use ($filter) {
                $query->where('position_id', $filter["position_id"]);
            })
            ->when(isset($filter["fill_status"]), function ($query) use ($filter) {
                $query->where('fill_status', $filter["fill_status"]);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate();
    }
    public function getAllManpowerRequest($filter = [])
    {
        $userId = auth()->user()->id;
        return ManpowerRequest::requestStatusPending()
            ->with(['user.employee', "position"])
            ->when(isset($filter["date_required"]) && $filter["date_required"], function ($query) use ($filter) {
                $query->where('date_required', $filter["date_required"]);
            })
            ->when(isset($filter["date_requested"]) && $filter["date_requested"], function ($query) use ($filter) {
                $query->where('date_requested', $filter["date_requested"]);
            })
            ->when(isset($filter["position_id"]), function ($query) use ($filter) {
                $query->where('position_id', $filter["position_id"]);
            })
            ->when(isset($filter["fill_status"]), function ($query) use ($filter) {
                $query->where('fill_status', $filter["fill_status"]);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }
    public function getMyRequest($filter = [])
    {
        return ManpowerRequest::myRequests()
            ->with('user.employee')
            ->when(isset($filter["date_required"]) && $filter["date_required"], function ($query) use ($filter) {
                $query->where('date_required', $filter["date_required"]);
            })
            ->when(isset($filter["date_requested"]) && $filter["date_requested"], function ($query) use ($filter) {
                $query->where('date_requested', $filter["date_requested"]);
            })
            ->when(isset($filter["position_id"]), function ($query) use ($filter) {
                $query->where('position_id', $filter["position_id"]);
            })
            ->when(isset($filter["fill_status"]), function ($query) use ($filter) {
                $query->where('fill_status', $filter["fill_status"]);
            })
            ->paginate();
    }
    public function getMyApprovals($filter = [])
    {
        $userId = auth()->user()->id;
        return ManpowerRequest::requestStatusPending()
            ->with(['user.employee', "position"])
            ->when(isset($filter["date_required"]) && $filter["date_required"], function ($query) use ($filter) {
                $query->where('date_required', $filter["date_required"]);
            })
            ->when(isset($filter["date_requested"]) && $filter["date_requested"], function ($query) use ($filter) {
                $query->where('date_requested', $filter["date_requested"]);
            })
            ->when(isset($filter["position_id"]), function ($query) use ($filter) {
                $query->where('position_id', $filter["position_id"]);
            })
            ->when(isset($filter["fill_status"]), function ($query) use ($filter) {
                $query->where('fill_status', $filter["fill_status"]);
            })
            ->myApprovals()
            ->orderBy('created_at', 'desc')
            ->paginate();
    }
    public function createManpowerRequest(array $attributes)
    {

        $main = $this->manpowerRequest->fill($attributes);
        $main->job_description_attachment = $this->uploadFile($attributes['job_description_attachment'], ManpowerRequest::JDA_DIR);

        if ($main->save()) {
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
    public function getOpenPositions($filter = [])
    {
        return $this->manpowerRequest
            ->with("position", "user.employee")
            ->where('fill_status', FillStatuses::OPEN->value)
            ->when(isset($filter["date_required"]) && $filter["date_required"], function ($query) use ($filter) {
                $query->where('date_required', $filter["date_required"]);
            })
            ->when(isset($filter["date_requested"]) && $filter["date_requested"], function ($query) use ($filter) {
                $query->where('date_requested', $filter["date_requested"]);
            })
            ->when(isset($filter["position_id"]), function ($query) use ($filter) {
                $query->where('position_id', $filter["position_id"]);
            })
            ->when(isset($filter["fill_status"]), function ($query) use ($filter) {
                $query->where('fill_status', $filter["fill_status"]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate();
    }
    public function getApprovedPositions($filter = [])
    {
        return $this->manpowerRequest
            ->with("position", "user.employee")
            ->where('request_status', RequestStatuses::APPROVED->value)
            ->when(isset($filter["date_required"]) && $filter["date_required"], function ($query) use ($filter) {
                $query->where('date_required', $filter["date_required"]);
            })
            ->when(isset($filter["date_requested"]) && $filter["date_requested"], function ($query) use ($filter) {
                $query->where('date_requested', $filter["date_requested"]);
            })
            ->when(isset($filter["position_id"]), function ($query) use ($filter) {
                $query->where('position_id', $filter["position_id"]);
            })
            ->when(isset($filter["fill_status"]), function ($query) use ($filter) {
                $query->where('fill_status', $filter["fill_status"]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate();
    }
}
