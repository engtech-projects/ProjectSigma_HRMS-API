<?php

namespace App\Models;

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

    protected function name(): Attribute
    {
        if ($this->assignment_type == AttendancePortal::DEPARTMENT) {
            return Attribute::make(
                get: fn () => $this->assignment->department_name,
            );
        }
        if ($this->assignment_type == AttendancePortal::PROJECT) {
            return Attribute::make(
                get: fn () => $this->assignment->project_code,
            );
        }
    }

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
    public function departments()
    {
        return $this->morphedByMany(Department::class, 'assignment', 'att_port_assigns');
    }
    public function projects()
    {
        return $this->morphedByMany(Project::class, 'assignment', 'att_port_assigns');
    }
    /**
     * ==================================================
     * MODEL ATTRIBUTES
     * ==================================================
     */
    public function getDepartmentNamesAttribute()
    {
        // return "department names";
        return implode(", ", $this->departments()->pluck("department_name")->toArray());
    }

    public function getProjectNamesAttribute()
    {
        // return "project names";
        return implode(", ", $this->projects()->pluck("project_code")->toArray());
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
