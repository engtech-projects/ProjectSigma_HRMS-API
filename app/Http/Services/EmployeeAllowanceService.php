<?php

namespace App\Http\Services;

use App\Enums\RequestApprovalStatus;
use App\Models\AllowanceRequest;
use App\Models\EmployeeAllowances;

class EmployeeAllowanceService
{
    protected $employeeAllowanceRequest;
    public function __construct(EmployeeAllowances $employeeAllowanceRequest)
    {
        $this->employeeAllowanceRequest = $employeeAllowanceRequest;
    }

    public function getAll()
    {
        return AllowanceRequest::with(['employee_allowances','charge_assignment'])->get();
    }

    public function getMyRequests()
    {
        return AllowanceRequest::with(['employee_allowances','charge_assignment'])
        ->where("created_by", auth()->user()->id)
        ->get();
    }

    public function getMyApprovals()
    {
        $userId = auth()->user()->id;
        $result = AllowanceRequest::with(['employee_allowances', 'charge_assignment'])
            ->requestStatusPending()
            ->authUserPending()
            ->get();
        return $result->filter(function ($item) use ($userId) {
            $nextPendingApproval = $item->getNextPendingApproval();
            return ($nextPendingApproval && $userId === $nextPendingApproval['user_id']);
        });
    }
}
