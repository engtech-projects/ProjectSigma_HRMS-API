<?php

namespace App\Models\Traits;

use App\Models\Project;
use App\Models\Employee;
use App\Models\Pivot\ProjectMemberPivot;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasProjectMember
{

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_members', 'project_id', 'employee_id')
            ->withPivot([
                'project_id',
                'employee_id'
            ])
            ->withtimestamps();
    }
}
