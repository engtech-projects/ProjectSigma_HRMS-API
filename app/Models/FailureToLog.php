<?php

namespace App\Models;

use App\Enums\AttendanceLogType;
use App\Models\Traits\HasApproval;
use App\Models\Traits\HasEmployee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailureToLog extends Model
{
    use HasFactory;
    use HasEmployee, HasApproval;

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
        'approval' => 'json',
        'employee_id' => 'integer',
    ];
}
