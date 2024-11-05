<?php

namespace App\Models;

use App\Enums\DisbursementStatus;
use App\Enums\RequestStatuses;
use App\Traits\HasApproval;
use App\Traits\ModelHelpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestSalaryDisbursement extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasApproval;
    use ModelHelpers;

    protected $fillable = [
        'payroll_date',
        'payroll_type',
        'release_type',
        'request_status',
        'disbursement_status',
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
    public function payroll_records(): BelongsToMany
    {
        return $this->belongsToMany(PayrollRecord::class, "request_salary_disbursement_payroll_records");
    }
    /**
     * ==================================================
     * MODEL ATTRIBUTES
     * ==================================================
     */
    public function getPayrollDateHumanAttribute()
    {
        return Carbon::parse($this->payroll_date)->format("F j, Y");
    }

    /**
     * ==================================================
     * STATIC SCOPES
     * ==================================================
     */
    public function scopeDisbursementStatusReleased(Builder $query)
    {
        return $query->where('disbursement_status', DisbursementStatus::RELEASED);
    }

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
        // temporary, to be removed. Change to PROCESSING. when Accounting is implemented add function to change status to RELEASED
        $this->disbursement_status = DisbursementStatus::RELEASED->value;
        $this->save();
        $this->refresh();
    }
}
