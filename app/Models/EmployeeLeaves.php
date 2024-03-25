<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class EmployeeLeaves extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    protected $fillable = [
        'id',
        'employee_id',
        'department_id',
        'project_id',
        'type',
        'other_absence',
        'date_of_absence_from',
        'date_of_absence_to',
        'reason_for_absence',
        'approvals',
        'request_status',
    ];
}
