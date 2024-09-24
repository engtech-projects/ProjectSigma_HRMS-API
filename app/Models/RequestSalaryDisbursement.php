<?php

namespace App\Models;

use App\Traits\HasApproval;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestSalaryDisbursement extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasApproval;

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
}
