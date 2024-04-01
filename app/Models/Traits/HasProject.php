<?php

namespace App\Models\Traits;

use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasProject
{

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

}
