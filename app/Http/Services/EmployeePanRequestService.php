<?php

namespace App\Http\Services;

use App\Enums\RequestStatuses;
use App\Models\EmployeePanRequest;
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
        return EmployeePanRequest::with(['employee', 'jobapplicantonly', 'department', 'salarygrade.salary_grade_level', 'position'])
        ->orderBy('created_at', 'desc')
        ->paginate(config("app.pagination_per_page", 10));
    }

    public function create($attributes)
    {
        $attributes["request_status"] = RequestStatuses::PENDING->value;
        $attributes["created_by"] = auth()->user()->id;
        $main = EmployeePanRequest::create($attributes);
        $main->projects()->sync($attributes['projects']);
        $main->save();
        $main->refresh();
        $main->notifyNextApprover(PanRequestForApproval::class);
        return $main;
    }
    public function getMyRequests()
    {
        return EmployeePanRequest::with(['employee', 'jobapplicantonly', 'department', 'salarygrade.salary_grade_level', 'position'])
            ->myRequests()
            ->paginate(config("app.pagination_per_page", 10));
    }
    public function getMyApprovals()
    {
        return EmployeePanRequest::with(['employee', 'jobapplicantonly', 'department', 'salarygrade.salary_grade_level', 'position'])
            ->myApprovals()
            ->paginate(config("app.pagination_per_page", 10));
    }
}
