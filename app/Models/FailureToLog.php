<?php

namespace App\Models;

use App\Enums\AttendanceLogType;
use App\Models\Traits\HasApproval;
use App\Models\Traits\HasEmployee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FailureToLog extends Model
{
    use HasFactory, SoftDeletes;
    use HasEmployee;

    protected $fillable = [
        'date',
        'time',
        'log_type',
        'reason',
        'approvals',
        'employee_id',
    ];
    protected $cast = [
        'date' => 'date:Y-m-d',
        'time' => 'time:H:i:s',
        'log_type' => AttendanceLogType::class,
        'approval' => 'array',
        'employee_id' => 'integer',
    ];


}
