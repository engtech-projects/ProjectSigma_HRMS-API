<?php

namespace App\Models\Traits;

use App\Models\Project;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasProject
{
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

}
