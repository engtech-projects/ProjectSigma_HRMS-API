<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ManpowerRequestJobApplicants extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $table = "manpower_request_job_applicants";

    protected $fillable = [
        'id',
        'job_applicants_id',
        'manpowerrequests_id',
        'remarks',
        'processing_checklist',
        'hiring_status',
    ];

    protected $casts = [
        'processing_checklist' => 'array'
    ];

    public function manpowerRequest()
    {
        return $this->belongsTo(ManpowerRequest::class, 'manpowerrequests_id', 'id');
    }

    public function jobApplicant()
    {
        return $this->belongsTo(JobApplicants::class, 'job_applicants_id', 'id');
    }
}
