<?php

namespace App\Models\Traits;

use App\Models\Employee;
use App\Models\Pivot\ProjectMemberPivot;
use App\Models\Project;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasProjectMember
{
    public function project_employee(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_employees', 'project_id', 'employee_id')
            ->using(ProjectMemberPivot::class)
            ->withPivot([
                'project_id',
                'employee_id'
            ])
            ->withtimestamps();
    }
    public function project_members(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'project_employees', 'project_id', 'employee_id')
            ->using(ProjectMemberPivot::class)
            ->withPivot([
                'project_id',
                'employee_id'
            ])
            ->withtimestamps();
    }

}
