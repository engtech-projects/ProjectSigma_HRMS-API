<?php

namespace App\Models;

use App\Enums\AttendanceLogType;
use App\Enums\AttendanceType;
use App\Models\Traits\HasDepartment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceLog extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasDepartment;

    protected $fillable = [
        'date',
        'time',
        'log_type',
        'attendance_type',
        'project_id',
        'department_id',
        'employee_id',
    ];

    protected $cast = [
        'date' => 'date:Y-m-d',
        'time' => 'time:H:i:s',
        'attendace_type' => AttendanceType::class,
        'log_type' => AttendanceLogType::class,
        'project_id' => 'integer',
        'department_id' => 'integer',
        'employee_id' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
