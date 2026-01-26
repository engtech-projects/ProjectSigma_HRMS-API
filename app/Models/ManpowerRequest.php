<?php

namespace App\Models;

use App\Enums\FillStatuses;
use App\Enums\RequestStatuses;
use App\Traits\HasApproval;
use App\Traits\ModelHelpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class ManpowerRequest extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasApproval;
    use ModelHelpers;

    public const JDA_DIR = "manpower/jda/";

    protected $fillable = [
        'id',
        'requesting_department',
        'date_requested',
        'date_required',
        'position_id',
        'employment_type',
        'brief_description',
        'job_description_attachment',
        'nature_of_request',
        'age_range',
        'status',
        'gender',
        'educational_requirement',
        'preferred_qualifications',
        'remarks',
        'charged_to',
        'breakdown_details',
        'fill_status',
        'approvals',
        'request_status',
        'created_by',
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
            $oldfileUniqueFolder = explode("/", $model->job_description_attachment);
            array_pop($oldfileUniqueFolder);
            Storage::deleteDirectory("public/" . implode("/", $oldfileUniqueFolder)); // DELETE FILE
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
    public function job_applicants()
    {
        return $this->belongsToMany(JobApplicants::class, 'manpower_request_job_applicants', 'manpowerrequests_id', 'job_applicants_id')->withPivot("id", "hiring_status", "processing_checklist", "remarks");
    }

    public function position()
    {
        return $this->belongsTo(Position::class, "position_id", "id");
    }

    public function department()
    {
        return $this->belongsTo(Department::class, "requesting_department", "id");
    }

    public function manpowerRequestJobApplicants()
    {
        return $this->hasMany(ManpowerRequestJobApplicants::class, 'manpowerrequests_id', 'id');
    }

    /**
     * MODEL
     * LOCAL SCOPES
     */

    public function scopeForHiring(Builder $query): void
    {
        $query->where('request_status', RequestStatuses::APPROVED);
    }

    public function scopeBetweenDates(Builder $query, $dateFrom, $dateTo): void
    {
        $query->whereBetween('date_requested', [$dateFrom, $dateTo]);
    }

    public function completeRequestStatus()
    {
        $this->request_status = RequestStatuses::APPROVED->value;
        $this->fill_status = FillStatuses::OPEN->value;
        $this->save();
        $this->refresh();
    }

    public function requestStatusCompleted(): bool
    {
        if ($this->request_status == RequestStatuses::APPROVED) {
            return true;
        }
        return false;
    }

    public function getDaysDelayedFilingAttribute()
    {
        $createdAt = Carbon::parse($this->created_at);
        $dateRequested = Carbon::parse($this->date_requested);
        return $createdAt->diffInDays($dateRequested) > 0 ? $createdAt->diffInDays($dateRequested) : 0;
    }

    public function getDateRequestedHumanAttribute()
    {
        $data = $this->date_requested;
        if ($data) {
            $data = Carbon::parse($this->date_requested)->format('F j, Y');
        } else {
            $data = "Date Requested N/A";
        }
        return $data;
    }

    public function getDateRequiredHumanAttribute()
    {
        $data = $this->date_required;
        if ($data) {
            $data = Carbon::parse($this->date_required)->format('F j, Y');
        } else {
            $data = "Date Required N/A";
        }
        return $data;
    }
}
