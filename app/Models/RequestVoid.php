<?php

namespace App\Models;

use App\Enums\RequestStatuses;
use App\Enums\VoidRequestModels;
use App\Traits\HasApproval;
use App\Traits\ModelHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class RequestVoid extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasApproval;
    use ModelHelpers;

    protected $fillable = [
        'request_type',
        'request_id',
        'reason_for_void',
        'request_status',
        'approvals',
        'created_by',
    ];
    protected $casts = [
        "approvals" => 'array'
    ];

    /**
     * ==================================================
     * MODEL RELATIONSHIPS
     * ==================================================
     */

    public function request()
    {
        return $this->morphTo();
    }

    /**
     * ==================================================
     * MODEL ATTRIBUTES
     * ==================================================
     */
    public function getVoidTypeNameAttribute()
    {
        return VoidRequestModels::toArraySwapped()[$this->request_type] ?? $this->request_type;
    }
    /**
     * ==================================================
     * STATIC SCOPES
     * ==================================================
     */

    /**
     * ==================================================
     * DYNAMIC SCOPES
     * ==================================================
     */

    /**
     * ==================================================
     * MODEL FUNCTIONS
     * ==================================================
     */
    public function completeRequestStatus()
    {
        $this->request_status = RequestStatuses::APPROVED->value;
        $this->save();
        $this->request->voidRequestStatus();
        $this->refresh();
    }

}
