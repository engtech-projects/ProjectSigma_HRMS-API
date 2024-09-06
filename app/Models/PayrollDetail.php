<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollDetail extends Model
{
    use HasFactory;

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
        "sss_employee_compensation",
        "sss_employer_compensation",
        "sss_employee_contribution",
        "sss_employer_contribution",
        "philhealth_employee_contribution",
        "philhealth_employer_contribution",
        "pagibig_employee_contribution",
        "pagibig_employer_contribution",
        "withholdingtax_contribution",
        "total_deduct",
        "net_pay",
    ];

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

    public function adjustments(): HasMany
    {
        return $this->hasMany(PayrollDetailsAdjustment::class, 'payroll_details_id');
    }

    public function charges(): HasMany
    {
        return $this->hasMany(PayrollDetailsCharging::class, 'payroll_details_id');
    }
    public function getTotalSssContributionAttribute()
    {
        return $this->sss_employee_contribution + $this->sss_employer_contribution;
    }
    public function getTotalSssCompensationAttribute()
    {
        return $this->sss_employee_compensation + $this->sss_employer_compensation;
    }
    public function getTotalSssAttribute()
    {
        return $this->total_sss_contribution + $this->total_sss_compensation;
    }
    public function getTotalPagibigContributionAttribute()
    {
        return $this->pagibig_employee_contribution + $this->pagibig_employer_contribution;
    }
    public function getTotalPagibigCompensationAttribute()
    {
        return $this->pagibig_employee_compensation + $this->pagibig_employer_compensation;
    }
    public function getTotalPagibigAttribute()
    {
        return $this->total_pagibig_contribution + $this->total_pagibig_compensation;
    }

    public function getTotalPhilhealthContributionAttribute()
    {
        return $this->philhealth_employee_contribution + $this->philhealth_employer_contribution;
    }
}
