<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternalWorkExperienceProjects extends Model
{
    use HasFactory;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'internal_work_experience_id',
        'project_id',
    ];
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    public function internalWorkExperience(): BelongsTo
    {
        return $this->belongsTo(InternalWorkExperience::class);
    }
}
