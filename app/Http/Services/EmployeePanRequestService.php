<?php

namespace App\Http\Services;

use App\Models\EmployeePanRequest;
use App\Models\Users;
use App\Notifications\PanRequestForApproval;

class EmployeePanRequestService
{
    protected $panRequest;
    public function __construct(EmployeePanRequest $panRequest)
    {
        $this->panRequest = $panRequest;
    }

    public function getAll()
    {
        return EmployeePanRequest::with(['employee', 'jobapplicantonly', 'department', 'salarygrade.salary_grade_level', 'position'])->orderBy('created_at', 'desc')->get();
    }

    public function create($attributes)
    {
        $main = EmployeePanRequest::create($attributes);
        $main->refresh();
        if ($main->getNextPendingApproval()) {
            Users::find($main->getNextPendingApproval()['user_id'])->notify(new PanRequestForApproval($main));
        }
        return $main;
    }
    public function getMyRequests()
    {
        return EmployeePanRequest::with(['employee', 'jobapplicantonly', 'department', 'salarygrade.salary_grade_level', 'position'])
            ->myRequests()
            ->get();
    }
    public function getMyApprovals()
    {
        return EmployeePanRequest::with(['employee', 'jobapplicantonly', 'department', 'salarygrade.salary_grade_level', 'position'])
            ->myApprovals()
            ->get();
    }
}
