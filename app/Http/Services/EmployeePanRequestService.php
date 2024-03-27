<?php

namespace App\Http\Services;

use Exception;
use App\Models\Employee;
use App\Models\InternalWorkExperience;
use App\Exceptions\TransactionFailedException;
use App\Enums\EmployeeInternalWorkExperiencesStatus;
use App\Models\EmployeePersonnelActionNoticeRequest;

class EmployeePanRequestService
{
    protected $panRequest;
    public function __construct(EmployeePersonnelActionNoticeRequest $panRequest)
    {
        $this->panRequest = $panRequest;
    }

    public function getAll()
    {
        return EmployeePersonnelActionNoticeRequest::with(['employee', 'jobapplicantonly', 'department'])->get();
    }

    public function create($attributes)
    {
        return EmployeePersonnelActionNoticeRequest::create($attributes);
    }
    public function getMyRequests()
    {
        return EmployeePersonnelActionNoticeRequest::with(['employee', 'jobapplicantonly', 'department'])
            ->where("created_by", auth()->user()->id)
            ->get();
    }
    public function getMyApprovals()
    {
        $userId = auth()->user()->id;
        $result = EmployeePersonnelActionNoticeRequest::with(['employee', 'jobapplicantonly', 'department'])
            ->requestStatusPending()
            ->authUserPending()
            ->get();
        return $result->filter(function ($item) use ($userId) {
            $nextPendingApproval = $item->getNextPendingApproval();
            return ($nextPendingApproval && $userId === $nextPendingApproval['user_id']);
        });
    }
}
