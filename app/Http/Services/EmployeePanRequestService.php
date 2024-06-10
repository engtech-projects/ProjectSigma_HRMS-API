<?php

namespace App\Http\Services;

use App\Models\EmployeePanRequest;

class EmployeePanRequestService
{
    protected $panRequest;
    public function __construct(EmployeePanRequest $panRequest)
    {
        $this->panRequest = $panRequest;
    }

    public function getAll()
    {
        return EmployeePanRequest::with(['employee', 'jobapplicantonly', 'department', 'salarygrade'])->get();
    }

    public function create($attributes)
    {
        return EmployeePanRequest::create($attributes);
    }
    public function getMyRequests()
    {
        return EmployeePanRequest::with(['employee', 'jobapplicantonly', 'department', 'salarygrade'])
            ->where("created_by", auth()->user()->id)
            ->get();
    }
    public function getMyApprovals()
    {
        $userId = auth()->user()->id;
        $result = EmployeePanRequest::with(['employee', 'jobapplicantonly', 'department', 'salarygrade'])
            ->requestStatusPending()
            ->authUserPending()
            ->get();
        return $result->filter(function ($item) use ($userId) {
            $nextPendingApproval = $item->getNextPendingApproval();
            return ($nextPendingApproval && $userId === $nextPendingApproval['user_id']);
        });
    }
}
