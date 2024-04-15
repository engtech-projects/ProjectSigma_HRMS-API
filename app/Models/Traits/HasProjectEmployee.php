<?php

namespace App\Models\Traits;

use App\Models\Employee;
use App\Models\Pivot\ProjectMemberPivot;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasProjectEmployee
{
    public function projet_has_employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'project_employees')
            ->withPivot([
                'project_id',
                'employee_id'
            ])
            ->withtimestamps();
    }
    public function employee_has_projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_employees')
            ->using(ProjectMemberPivot::class)
            ->withPivot([
                'project_id',
                'employee_id'
            ])
            ->withtimestamps();
    }

    public function scopeEmployeeLastestProject(Builder $query): Builder
    {
        return $query->latest('created_at');
    }
}
