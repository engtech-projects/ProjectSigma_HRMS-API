<?php

namespace App\Models;

use App\Traits\HasApproval;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ManpowerRequest extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasApproval;

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

    protected $casts = [
        'approvals' => 'array'
    ];

    public static function boot()
    {
        parent::boot();
        static::deleted(function ($model) {
            $attachment = explode("/", $model->job_description_attachment);
            Storage::deleteDirectory("public/" . $attachment[0] . "/" . $attachment[1]);
        });
    }

    /**
     * MODEL
     * RELATIONS
     * */
    public function getDataUserIdAttribute()
    {
        return $this->data['user_id'] ?? null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function job_applicants()
    {
        return $this->hasMany(JobApplicants::class, 'manpowerrequests_id', 'id');
    }

    public function scopeRequestStatusPending(Builder $query): void
    {
        $query->where('request_status', 'Pending');
    }
}
