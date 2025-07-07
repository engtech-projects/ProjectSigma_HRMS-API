<?php

namespace App\Http\Services;

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
        return AllowanceRequest::with(['employee_allowances','charge_assignment'])->paginate(15);
    }
    public function getMyRequests()
    {
        return AllowanceRequest::with(['employee_allowances','charge_assignment'])
        ->myRequests()
        ->paginate(15);
    }
    public function getMyApprovals()
    {
        return AllowanceRequest::with(['employee_allowances', 'charge_assignment'])->myApprovals()->paginate(15);
    }
}
