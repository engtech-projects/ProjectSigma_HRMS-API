<?php

namespace App\Http\Services;

use App\Enums\RequestApprovalStatus;
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
        return EmployeeAllowances::with('charge_assignment')->get();
    }

    public function getMyApprovals()
    {
        $userId = auth()->user()->id;
        $result = EmployeeAllowances::with("charge_assignment")
            ->requestStatusPending()
            ->authUserPending()
            ->get();
        return $result->filter(function ($item) use ($userId) {
            $nextPendingApproval = $item->getNextPendingApproval();
            return ($nextPendingApproval && $userId === $nextPendingApproval['user_id']);
        });
    }
}
