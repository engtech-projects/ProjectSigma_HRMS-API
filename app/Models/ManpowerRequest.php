<?php

namespace App\Models;

use App\Traits\HasUser;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManpowerRequest extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

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
    ];

    protected $cast = [
        'approvals' => 'array'
    ];

    /**
     * MODEL
     * RELATIONS
     * */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function scopeApprovalStatusPendingAndApproved($query)
    {
        $query->whereJsonContains('approvals', ['status' => ['Pending', 'Approved']]);
    }
}
