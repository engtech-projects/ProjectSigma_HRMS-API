<?php

namespace App\Models\Traits;

use App\Models\Employee;
use App\Models\Project;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasProjectEmployee
{
    public function employee_has_projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_employees', 'employee_id', 'project_id')
            ->withPivot([
                'project_id',
                'employee_id'
            ])
            ->withtimestamps();
    }
    public function project_has_employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'project_employees', 'employee_id', 'project_id')
            ->withPivot([
                'project_id',
                'employee_id'
            ])
            ->withtimestamps();
    }
}
