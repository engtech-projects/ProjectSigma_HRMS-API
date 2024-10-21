<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'assignment_type',
        'assignment_id',
        'portal_token',
        'last_used',
    ];

    public function assignment(): MorphTo
    {
        return $this->morphTo();
    }
    public function department(): MorphTo
    {
        return $this->morphTo()->where('assignment_type', '=', Department::class);
    }

    public function project(): MorphTo
    {
        return $this->morphTo()->where('assignment_type', '=', Project::class);
    }

    public function getAssignmentNameAttribute()
    {
        if ($this->assignment_type == AttendancePortal::DEPARTMENT) {
            return $this->assignment->department_name;
        }
        if ($this->assignment_type == AttendancePortal::PROJECT) {
            return $this->assignment->project_code;
        }
    }
}
