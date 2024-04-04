<?php

namespace App\Models\Pivot;

use App\Models\Employee;
use App\Models\Traits\HasEmployee;
use App\Models\Traits\HasProject;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectMemberPivot extends Pivot
{
    use HasProject;
    protected $table = "project_employees";

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
