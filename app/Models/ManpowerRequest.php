<?php

namespace App\Models;

use App\Enums\ManpowerRequestStatus;
use App\Traits\HasApproval;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ManpowerRequest extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasApproval;

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

    /**
     * MODEL
     * STATIC METHODS
     */
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
     * ATTRIBUTES
     */

    public function getDataUserIdAttribute()
    {
        return $this->data['user_id'] ?? null;
    }

    /**
     * MODEL
     * RELATED RELATIONS
     * */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function job_applicants()
    {
        return $this->hasMany(JobApplicants::class, 'manpowerrequests_id', 'id');
    }

    /**
     * MODEL
     * LOCAL SCOPES
     */

    public function scopeRequestStatusPending(Builder $query): void
    {
        $query->where('request_status', 'Pending');
    }

    public function completeRequestStatus()
    {
        $this->request_status = ManpowerRequestStatus::APPROVED;
        $this->save();
        $this->refresh();
    }
    public function denyRequestStatus()
    {

        $this->request_status = ManpowerRequestStatus::DISAPPROVED;
        $this->save();
        $this->refresh();
    }

    public function requestStatusCompleted(): bool
    {
        if ($this->request_status == ManpowerRequestStatus::APPROVED) {
            return true;
        }
        return false;
    }

    public function requestStatusEnded(): bool
    {
        if (
            in_array(
                $this->request_status,
                [
                    ManpowerRequestStatus::DISAPPROVED,
                    ManpowerRequestStatus::FILLED,
                    ManpowerRequestStatus::HOLD,
                    ManpowerRequestStatus::CANCELLED,
                ]
            )
        ) {
            return true;
        }
        return false;
    }
}
