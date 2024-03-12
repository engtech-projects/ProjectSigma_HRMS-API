<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class EmployeeEducation extends Model
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;
    protected $fillable = [
        'id',
        'employee_id',
        'elementary_name',
        'elementary_education',
        'elementary_period_attendance_to',
        'elementary_period_attendance_from',
        'elementary_year_graduated',
        'elementary_degree_earned_of_school',
        'elementary_honors_received',
        'secondary_name',
        'secondary_education',
        'secondary_period_attendance_to',
        'secondary_period_attendance_from',
        'secondary_year_graduated',
        'secondary_degree_earned_of_school',
        'secondary_honors_received',
        'vocationalcourse_name',
        'vocationalcourse_education',
        'vocationalcourse_period_attendance_to',
        'vocationalcourse_period_attendance_from',
        'vocationalcourse_year_graduated',
        'vocationalcourse_degree_earned_of_school',
        'vocationalcourse_honors_received',
        'college_name',
        'college_education',
        'college_period_attendance_to',
        'college_period_attendance_from',
        'college_year_graduated',
        'college_degree_earned_of_school',
        'college_honors_received',
        'graduatestudies_name',
        'graduatestudies_education',
        'graduatestudies_period_attendance_to',
        'graduatestudies_period_attendance_from',
        'graduatestudies_year_graduated',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
