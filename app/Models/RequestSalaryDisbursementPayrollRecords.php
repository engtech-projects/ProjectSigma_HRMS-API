<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestSalaryDisbursementPayrollRecords extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'employee_id',
    ];

    /**
     * ==================================================
     * MODEL RELATIONSHIPS
     * ==================================================
     */
    public function payroll_record(): BelongsTo
    {
        return $this->belongsTo(PayrollRecord::class);
    }
    public function request_salary_disbursement(): BelongsTo
    {
        return $this->belongsTo(RequestSalaryDisbursement::class);
    }
    /**
     * ==================================================
     * MODEL ATTRIBUTES
     * ==================================================
     */

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
