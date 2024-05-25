<?php

namespace App\Http\Services;

use App\Models\AttendanceLog;
use Illuminate\Support\Carbon;
use App\Http\Traits\Attendance;
use App\Http\Services\Payroll\PayrollDeduction;
use App\Models\SSSContribution;

class EmployeeService
{
    public function employeeDTR($employee, $date)
    {
        $schedules_attendances = $employee->applied_schedule_with_attendance($date);
        $events = $employee->events_dtr($date);
        $travelOrders = $employee->travel_order_dtr($date);
        $overtime = $employee->employee_overtime()->where('overtime_date', $date)->get();

        $leave = $employee->leave_dtr($date);
        $collection = [
            "schedules_attendances" => $schedules_attendances,
            "events" => $events,
            "overtime" => $overtime,
            "travel_orders" => $travelOrders,
            "leave" => $leave
        ];
        return [
            "schedules_attendances" => $schedules_attendances,
            "travel_order" => $travelOrders,
            "ovetime" => $overtime,
            "leave" => $employee->leave_dtr($date),
            "events" => $events,
            "metadata" => $employee->getMetaData($collection, $date),
        ];
    }

    public function generatePayroll(array $period, array $filters, $employee)
    {
        $total = 0;

        $dtr = collect($period)->groupBy(function ($period) use ($filters) {
            return $period["date"];
        })->map(function ($period) use ($employee, $filters) {
            $date = $period[0]["date"];
            $dtr = $this->employeeDTR($employee, $date);
            $grossPay =  $employee->salary_gross_pay($dtr["metadata"]);
            $dtr["grosspay"] = $grossPay;
            return $dtr;
        });

        $result = [
            "dtr" => $dtr,
            "salary_deduction" => $this->getSalaryDeduction($employee, $filters),

        ];

        $totalGrossPay = 0;
        foreach ($result["dtr"] as $value) {
            $grossPay = $value["grosspay"];
            $total = 0;
            foreach ($grossPay as $key => $value) {
                $totalGrossPay += $value["reg_hrs"] + $value["overtime"];
            }
            $totalGrossPay += $total;
        }

        $totalSalaryDeduction = $this->getTotalSalaryDeduction($result["salary_deduction"]);
        $totalNetPay = $totalGrossPay - $totalSalaryDeduction;
        $result["total_gross_pay"] = $totalGrossPay;
        $result["total_salary_deduction"] = $totalSalaryDeduction;
        $result["total_net_pay"] = $totalNetPay;
        return $result;
    }
    public function getSalaryDeduction($employee, $filters)
    {
        $salaryGrade = $employee->current_employment?->employee_salarygrade;
        $salary = $salaryGrade ? $salaryGrade->monthly_salary_amount : 0;

        $result = [
            "cash_advance" => $employee->cash_advance_deduction($salary, $filters["payroll_type"], $filters["payroll_date"]),
            "sss" => $filters["deduct_sss"] ? $employee->sss_deduction($salary, $filters["payroll_type"]) : [],
            "phic" => $filters["deduct_philhealth"] ? $employee->philhealth_deduction($salary, $filters["payroll_type"]) : [],
            "hmdf" => $filters["deduct_pagibig"] ? $employee->pagibig_deduction($salary, $filters["payroll_type"]) : [],
            "ewtc" =>  $employee->with_holding_tax_deduction($salary),
            "loan" => $employee->loan_deduction($salary, $filters["payroll_type"], $filters["payroll_date"]),
        ];

        return $result;
    }
    public function getTotalSalaryDeduction($deductions)
    {
        $cashAdvance = 0;
        $sss = 0;
        $phic = 0;
        $ewtc = 0;
        $loan = 0;
        $hmdf = 0;
        if ($deductions["cash_advance"]) {
            $cashAdvance = $deductions["cash_advance"];
        }
        if ($deductions["sss"]) {
            $sss = $deductions["sss"]["total_compensation"] + $deductions["sss"]["total_contribution"];
        }
        if ($deductions["phic"]) {
            $phic = $deductions["phic"]["total_compensation"];
        }
        if ($deductions["hmdf"]) {
            $hmdf = $deductions["hmdf"]["total_compensation"];
        }
        if ($deductions["ewtc"]) {
            $ewtc = $deductions["ewtc"];
        }
        if ($deductions["loan"]) {
            $loan = $deductions["loan"];
        }
        return $cashAdvance + $sss + $phic + $hmdf + $ewtc + $loan;
    }
}
