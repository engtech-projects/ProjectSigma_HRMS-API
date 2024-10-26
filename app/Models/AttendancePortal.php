<?php

namespace App\Models;

use App\Enums\AssignTypes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class AttendancePortal extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    public const DEPARTMENT = "App\Models\Department";
    public const PROJECT = "App\Models\Project";

    protected $appends = [
        'name',
    ];

    protected $fillable = [
        'id',
        'name_location',
        'ip_address',
        'portal_token',
        'last_used',
    ];

    /**
     * ==================================================
     * MODEL RELATIONSHIPS
     * ==================================================
     */
    public function assignment(): MorphTo
    {
        return $this->morphTo(); // KEPT FOR LEGACY SUPPORT
    }
    public function departments()
    {
        return $this->morphedByMany(Department::class, 'assignment', 'att_port_assigns', "attendance_portal_id", "assignment_id", "id", "id")
        ->withPivot('created_at', 'updated_at');
    }
    public function projects()
    {
        return $this->morphedByMany(Project::class, 'assignment', 'att_port_assigns', "attendance_portal_id", "assignment_id", "id", "id")
        ->withPivot('created_at', 'updated_at');
    }
    /**
     * ==================================================
     * MODEL ATTRIBUTES
     * ==================================================
     */
    // LEGACY ATTRIBUTES
    public function getTypeAttribute()
    {
        if ($this->assignment_type == Self::DEPARTMENT) {
            return AssignTypes::DEPARTMENT;
        }
        if ($this->assignment_type == Self::PROJECT) {
            return AssignTypes::PROJECT;
        }
        return "";
    }
    public function getNameAttribute()
    {
        if ($this->assignment_type == Self::DEPARTMENT) {
            return $this->assignment->department_name;
        }
        if ($this->assignment_type == Self::PROJECT) {
            return $this->assignment->project_code;
        }
        return "";
    }
    public function getDepartmentNamesAttribute()
    {
        return implode(", ", $this->departments()->pluck("department_name")->toArray());
    }
    public function getProjectNamesAttribute()
    {
        return implode(", ", $this->projects()->pluck("project_code")->toArray());
    }
    public function getDepartmentProjectNamesAttribute()
    {
        return implode(
            ", ",
            array_merge(
                $this->departments()->pluck("department_name")->toArray(),
                $this->projects()->pluck("project_code")->toArray(),
            )
        );
    }
    public function getAssignmentCountAttribute()
    {
        return $this->departments()->count() + $this->projects()->count();
    }
    /**
     * ==================================================
     * STATIC SCOPES
     * ==================================================
     */

    /**
     * ==================================================
     * DYNAMIC SCOPES
     * ==================================================
     */

    /**
     * ==================================================
     * MODEL FUNCTIONS
     * ==================================================
     */
}
