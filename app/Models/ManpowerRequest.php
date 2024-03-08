<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class ManpowerRequest extends Model
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;

    protected $fillable = [
        'id',
        'requesting_department',
        'date_requested',
        'date_required',
        'position',
        'employment_type',
        'brief_description',
        'job_description_attachment',
        'nature_of_request',
        'age_range',
        'status',
        'gender',
        'educational_requirement',
        'preferred_qualifications',
        'approvals',
        'remarks',
        'request_status',
        'charged_to',
        'breakdown_details',
        'requested_by',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(Users::class, "id", "requested_by");
    }

    public function department(): HasOne
    {
        return $this->hasOne(Department::class, "id", "requesting_department");
    }

    public function employee_address(): HasMany
    {
        return $this->hasMany(EmployeeAddress::class);
    }


    public function job_applicants(): HasMany
    {
        return $this->hasMany(JobApplicants::class,'manpowerrequests_id','id');
    }
}
