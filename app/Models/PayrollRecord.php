<?php

namespace App\Models;

use App\Enums\PostingStatusType;
use App\Enums\RequestStatuses;
use App\Enums\TermsOfPaymentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasApproval;
use App\Traits\ModelHelpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollRecord extends Model
{
    use HasFactory;
    use HasApproval;
    use SoftDeletes;
    use ModelHelpers;

    protected $fillable = [
        'charging_type',
        'project_id',
        'department_id',
        'payroll_type',
        'release_type',
        'payroll_date',
        'cutoff_start',
        'cutoff_end',
        'advance_days',
        'approvals',
        'request_status',
        'created_by',
    ];
    protected $casts = [
        "approvals" => 'array'
    ];
    /**
     * The roles that belong to the PayrollRecord
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function payroll_details(): HasMany
    {
        return $this->hasMany(PayrollDetail::class);
    }

    public function department(): HasOne
    {
        return $this->hasOne(Department::class, "id", "department_id");
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class, "id", "project_id");
    }

    public function salary_disbursement(): BelongsToMany
    {
        return $this->belongsToMany(RequestSalaryDisbursement::class, RequestSalaryDisbursementPayrollRecords::class, "payroll_record_id", "request_salary_disbursement_id");
    }

    public function charging()
    {
        if ($this->department_id) {
            return $this->department;
        }
        if ($this->project_id) {
            return $this->project;
        }
    }

    public function getChargingNameAttribute()
    {
        if ($this->project_id) {
            return $this->project->project_code;
        }
        if ($this->department_id) {
            return $this->department->department_name;
        }
        return 'No charging found.';
    }

    public function getProjectIdentifierNameAttribute()
    {
        if ($this->project_id) {
            return $this->project->employeeInternalWorks->first()?->work_location ? $this->project->employeeInternalWorks->first()?->work_location : 'No work location found.';
        }
        if ($this->department_id) {
            return "Office";
        }
        return 'No work location found.';
    }

    public function getPayrollDateHumanAttribute()
    {
        return Carbon::parse($this->payroll_date)->format("F j, Y");
    }

    public function getCutoffStartHumanAttribute()
    {
        return Carbon::parse($this->cutoff_start)->format("F j, Y");
    }

    public function getCutoffEndHumanAttribute()
    {
        return Carbon::parse($this->cutoff_end)->format("F j, Y");
    }

    public function scopeBetweenDates($query, $dateFrom, $dateTo)
    {
        $query->whereBetween('payroll_date', [$dateFrom, $dateTo]);
    }

    public function completeRequestStatus()
    {
        $this->request_status = RequestStatuses::APPROVED->value;
        $this->save();
        foreach ($this->payroll_details as $employeePayroll) {
            foreach ($employeePayroll->deductions as $deductions) {
                $deductions->deduction()->update(["posting_status" => PostingStatusType::POSTED->value]);
            }
            if ($this->advance_days > 0) {
                $employee = $employeePayroll->employee;
                $employeeDailyRate = $employee->current_employment->employee_salarygrade->dailyRate;
                $advanceAmount =  $employeeDailyRate * $this->advance_days;
                $employeePayroll->employee->other_deduction()->create([
                    'otherdeduction_name' => "Payroll Advance",
                    'terms_of_payment' => TermsOfPaymentType::MONTHLY->value,
                    'installment_deduction' => $advanceAmount * 10,
                    'amount' => $advanceAmount ,
                    'deduction_date_start' => $this->payroll_date,
                ]);
            }
        }
        $this->refresh();
    }
}
