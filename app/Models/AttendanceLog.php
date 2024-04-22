<?php

namespace App\Models;

use App\Enums\AttendanceLogType;
use App\Enums\AttendanceType;
use App\Models\Traits\HasDepartment;
use Carbon\Carbon;
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

    public function getFilterLate($query, $employee_id, $starttime, $start_date, $end_date)
    {
        return $query->where([
            ["employee_id", $employee_id],
            ["log_type", AttendanceLogType::TIME_IN],
            ["time", ">", $starttime]
        ])->whereBetween(
            'date',
            [
                $start_date,
                $end_date
            ]
        )->count();
    }

    public function getLate($query, $employee_id, $starttime)
    {
        return $query->where([
            ["employee_id", $employee_id],
            ["log_type", AttendanceLogType::TIME_IN],
            ["time", ">", $starttime]
        ])->whereBetween(
            'date',
            [
                Carbon::now()->startOfMonth()->format('Y-m-d'),
                Carbon::now()->endOfMonth()->format('Y-m-d')
            ]
        )->count();
    }

    public function getAttendance($query, $employee_id, $start_date, $end_date)
    {
        return $query->where([
            ["employee_id", $employee_id],
        ])->whereBetween(
            'date',
            [
                $start_date,
                $end_date
                // Carbon::now()->startOfMonth()->format('Y-m-d'),
                // Carbon::now()->endOfMonth()->format('Y-m-d')
            ]
        )->count();
    }
}
