<?php

namespace App\Models;

use App\Enums\InternalWorkExpStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'project_monitoring_id',
        'project_code',
        'status'
    ];

    protected $casts = [
        'project_code' => 'string',
        'project_monitoring_id' => 'integer',
        'created_at' => 'datetime:Y-m-d'
    ];

    /**
     * MODEL
     * RELATED
     * RELATION
     */
    public function employeeInternalWorks(): BelongsToMany
    {
        return $this->belongsToMany(InternalWorkExperience::class, InternalWorkExperienceProjects::class, 'project_id', 'internal_work_experience_id', 'id', 'id')
        ->where('status', InternalWorkExpStatus::CURRENT->value)
        ->withTimestamps();
    }
    public function employee_allowance(): MorphOne
    {
        return $this->morphOne(EmployeeAllowances::class, 'charge_assignment');
    }

    public function project_schedule()
    {
        return $this->hasMany(Schedule::class, "project_id");
    }
    public function schedules()
    {
        return $this->hasMany(Schedule::class, "project_id");
    }
    public function schedule_regular()
    {
        return $this->hasMany(Schedule::class, "project_id")->regularSchedules();
    }
    public function schedule_irregular()
    {
        return $this->hasMany(Schedule::class, "project_id")->irregularSchedules();
    }
}
