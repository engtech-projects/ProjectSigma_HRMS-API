<?php

namespace App\Models\Pivot;

use App\Models\Traits\HasEmployee;
use App\Models\Traits\HasProject;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectMemberPivot extends Pivot
{
    use HasEmployee, HasProject;

}
