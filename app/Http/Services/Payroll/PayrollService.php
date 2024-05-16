<?php

namespace App\Http\Services\Payroll;

use App\Http\Services\Payroll\PayrollDeduction;
use App\Http\Services\EmployeeService;
use App\Http\Traits\Attendance;

class PayrollService
{
    use Attendance;

    protected $payrollDeduction;
    protected $employeeService;
    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function generatePayroll(array $period, array $filters, $employee)
    {
        $salaryGrade = $employee->current_employment?->employee_salarygrade;
        $salary = $salaryGrade ? $salaryGrade->monthly_salary_amount : 0;
        $dtr = collect($period)->groupBy(function ($period) {
            return $period["date"];
        })->map(function ($period) use ($employee) {
            $date = $period[0]["date"];
            $dtr = $this->employeeService->employeeDTR($employee, $date);
            $dtr["gross"] = $this->getGrossPay($dtr);
            return $dtr;
        });

        return [
            "dtr" => $dtr,
            "monthly_salary" => $salary,
            "salary_deduction" => $this->getSalaryDeduction($employee, $filters),
        ];
    }
    public function getSalaryDeduction($employee, $filters)
    {

        $salaryGrade = $employee->current_employment?->employee_salarygrade;
        $salary = $salaryGrade ? $salaryGrade->monthly_salary_amount : 0;
        $salaryDeduction = new PayrollDeduction($employee, $salary, $filters);
        return [
            "cash_advance" => $salaryDeduction->cashAdvance->cashAdvance,
            "sss" => $filters["deduct_sss"] ? $salaryDeduction->sss : [],
            "phic" => $filters["deduct_philhealth"] ? $salaryDeduction->philhealth : [],
            "hmdf" => $filters["deduct_pagibig"] ? $salaryDeduction->pagibig : [],
            "ewtc" =>  $salaryDeduction->withHoldingTax,
            "loan" => $salaryDeduction->loan->loan
        ];
    }

    public function getGrossPay($dtr)
    {
        return $dtr;
    }
}
