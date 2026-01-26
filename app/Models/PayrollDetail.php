<?php

namespace App\Models;

use App\Enums\PayrollDetailsDeductionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "payroll_record_id",
        "employee_id",
        "regular_hours",
        "rest_hours",
        "regular_holiday_hours",
        "special_holiday_hours",
        "regular_overtime",
        "rest_overtime",
        "regular_holiday_overtime",
        "special_holiday_overtime",
        "regular_pay",
        "rest_pay",
        "regular_holiday_pay",
        "special_holiday_pay",
        "regular_ot_pay",
        "rest_ot_pay",
        "regular_holiday_ot_pay",
        "special_holiday_ot_pay",
        "gross_pay",
        "sss_employee_contribution",
        "sss_employer_contribution",
        "sss_employee_compensation",
        "sss_employer_compensation",
        "sss_employee_wisp",
        "sss_employer_wisp",
        "philhealth_employee_contribution",
        "philhealth_employer_contribution",
        "pagibig_employee_contribution",
        "pagibig_employer_contribution",
        "withholdingtax_contribution",
        "total_deduct",
        "net_pay",
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
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
    public function deductions(): HasMany
    {
        return $this->hasMany(PayrollDetailDeduction::class, 'payroll_details_id', 'id');
    }
    public function cashAdvancePayments(): HasMany
    {
        return $this->hasMany(PayrollDetailDeduction::class, 'payroll_details_id', 'id')
            ->where('type', PayrollDetailsDeductionType::CASHADVANCE);
    }
    public function loanPayments(): HasMany
    {
        return $this->hasMany(PayrollDetailDeduction::class, 'payroll_details_id', 'id')
            ->where('type', PayrollDetailsDeductionType::LOAN);
    }
    public function otherDeductionPayments(): HasMany
    {
        return $this->hasMany(PayrollDetailDeduction::class, 'payroll_details_id', 'id')
            ->where('type', PayrollDetailsDeductionType::OTHERDEDUCTION);
    }
    public function adjustments(): HasMany
    {
        return $this->hasMany(PayrollDetailsAdjustment::class, 'payroll_details_id');
    }
    public function charges(): HasMany
    {
        return $this->hasMany(PayrollDetailsCharging::class, 'payroll_details_id');
    }
    /**
     * ==================================================
     * MODEL ATTRIBUTES
     * ==================================================
     */
    public function getTotalSssContributionAttribute()
    {
        return $this->sss_employee_contribution + $this->sss_employer_contribution;
    }
    public function getTotalSssCompensationAttribute()
    {
        return $this->sss_employee_compensation + $this->sss_employer_compensation;
    }
    public function getTotalSssWispAttribute()
    {
        return $this->sss_employee_wisp + $this->sss_employer_wisp;
    }
    public function getTotalSssAttribute()
    {
        return $this->total_sss_contribution + $this->total_sss_compensation + $this->total_sss_wisp;
    }
    public function getTotalPagibigContributionAttribute()
    {
        return $this->pagibig_employee_contribution + $this->pagibig_employer_contribution;
    }
    public function getTotalPhilhealthContributionAttribute()
    {
        return $this->philhealth_employee_contribution + $this->philhealth_employer_contribution;
    }
    public function getTotalAdjustmentAttribute()
    {
        return $this->adjustments()->sum('amount');
    }
    public function getTotalSundayPaysAttribute()
    {
        return $this->regular_pay +
        $this->rest_ot_pay +
        $this->total_adjustment;
    }
    public function getTotalRegularHolidayPaysAttribute()
    {
        return $this->regular_holiday_pay +
            $this->regular_holiday_ot_pay +
            $this->total_adjustment;
    }
    public function getTotalSpecialHolidayPaysAttribute()
    {
        return $this->special_holiday_pay +
            $this->special_holiday_ot_pay +
            $this->total_adjustment;
    }
    public function getTotalBasicPaysAttribute()
    {
        return $this->regular_pay +
            $this->total_adjustment;
    }
    public function getTotalAllowanceAttribute()
    {
        return EmployeeAllowances::where("employee_id", $this->employee_id)->sum("allowance_amount");
    }
    public function getTotalOvertimePaysAttribute()
    {
        return $this->regular_ot_pay +
            $this->rest_ot_pay +
            $this->regular_holiday_ot_pay +
            $this->special_holiday_ot_pay +
            $this->rest_pay +
            $this->regular_holiday_pay +
            $this->special_holiday_pay;
    }
    public function getTotalCashAdvancePaymentsAttribute()
    {
        $amt = 0;
        foreach ($this->cashAdvancePayments as $deductions) {
            $amt += $deductions->deduction?->amount_paid ?? 0;
        }
        return $amt;
    }
    public function getTotalLoanPaymentsAttribute()
    {
        $amt = 0;
        foreach ($this->loanPayments as $deductions) {
            $amt += $deductions->deduction?->amount_paid ?? 0;
        }
        return $amt;
    }
    public function getTotalOtherDeductionPaymentsAttribute()
    {
        $amt = 0;
        foreach ($this->otherDeductionPayments as $deductions) {
            $amt += $deductions->deduction?->amount_paid ?? 0;
        }
        return $amt;
    }
    public function getSalaryChargingNamesAttribute()
    {
        $names = [];
        foreach ($this->charges as $charge) {
            if (in_array($charge->name, PayrollDetailsCharging::BASIC_PAY_NAMES) || in_array($charge->name, PayrollDetailsCharging::OVERTIME_PAY_NAMES)) {
                $names[] = $charge->charging_name;
            }
        }
        return implode(", ", $names);
    }
    public function scopeHasSssContributions(Builder $query)
    {
        $query->where("sss_employee_contribution", ">", 0)
            ->orWhere("sss_employer_contribution", ">", 0)
            ->orWhere("sss_employee_compensation", ">", 0)
            ->orWhere("sss_employer_compensation", ">", 0)
            ->orWhere("sss_employee_wisp", ">", 0)
            ->orWhere("sss_employer_wisp", ">", 0);
    }
    public function scopeHasPagibigContributions(Builder $query)
    {
        $query->where("pagibig_employee_contribution", ">", 0)
            ->orWhere("pagibig_employer_contribution", ">", 0);
    }
    public function scopeHasPhilhealthContributions(Builder $query)
    {
        $query->where("philhealth_employee_contribution", ">", 0)
            ->orWhere("philhealth_employer_contribution", ">", 0);
    }
}
