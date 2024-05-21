<?php

namespace App\Models\Traits;

use App\Enums\RequestStatusType;
use App\Models\CashAdvance;
use App\Models\SalaryGradeStep;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait EmployeePayroll
{
    public function cash_advance_payroll(): HasMany
    {
        return $this->hasMany(CashAdvance::class)->where('request_status', RequestStatusType::APPROVED->value);
    }
    public function salary_grade_payroll(): HasOne
    {
        return $this->hasOne(SalaryGradeStep::class);
    }

    public function salary_gross_pay($dtr)
    {
        $salaryGrade = $this->current_employment?->employee_salarygrade;
        $dailyRate = $salaryGrade?->dailyRate ?: 0;
        $result = [];
        foreach ($dtr as $key => $value) {
            $result[$key]["reg_hrs"] = $value["reg_hrs"] * $dailyRate;
            $result[$key]["overtime"] = $value["overtime"] * $dailyRate;
        }
        return $result;
    }
}
