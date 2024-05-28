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
            "leave" => $leave,
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

        $dtrs = $dtr->values();
        $result = [
            "dtr" => $dtr,
            "salary_deduction" => $this->getSalaryDeduction($employee, $filters),
        ];

        $totalHoursWorked = [
            "regular" => [
                "reg_hrs" => round($dtrs->sum("metadata.regular.reg_hrs"), 2),
                "overtime" => round($dtrs->sum("metadata.regular.overtime"), 2),
                "late" => round($dtrs->sum("metadata.regular.late"), 2),
                "undertime" => round($dtrs->sum("metadata.regular.undertime"), 2),
            ],
            "rest" => [
                "reg_hrs" => round($dtrs->sum("metadata.rest.reg_hrs"), 2),
                "overtime" => round($dtrs->sum("metadata.rest.overtime"), 2),
                "late" => round($dtrs->sum("metadata.rest.late"), 2),
                "undertime" => round($dtrs->sum("metadata.rest.undertime"), 2),
            ],
            "regular_holidays" => [
                "reg_hrs" => round($dtrs->sum("metadata.regular_holidays.reg_hrs"), 2),
                "overtime" => round($dtrs->sum("metadata.regular_holidays.overtime"), 2),
                "late" => round($dtrs->sum("metadata.regular_holidays.late"), 2),
                "undertime" => round($dtrs->sum("metadata.regular_holidays.undertime"), 2),
            ],
            "special_holidays" => [
                "reg_hrs" => round($dtrs->sum("metadata.special_holidays.reg_hrs"), 2),
                "overtime" => round($dtrs->sum("metadata.special_holidays.overtime"), 2),
                "late" => round($dtrs->sum("metadata.special_holidays.late"), 2),
                "undertime" => round($dtrs->sum("metadata.special_holidays.undertime"), 2),
            ]
        ];
        $grossPays = collect([
            "regular" => [
                "regular" => round($dtrs->sum("grosspay.regular.reg_hrs"), 2),
                "overtime" => round($dtrs->sum("grosspay.regular.overtime"), 2),
            ],
            "rest" => [
                "regular" => round($dtrs->sum("grosspay.rest.reg_hrs"), 2),
                "overtime" => round($dtrs->sum("grosspay.rest.overtime"), 2),
            ],
            "regular_holidays" => [
                "regular" => round($dtrs->sum("grosspay.regular_holidays.reg_hrs"), 2),
                "overtime" => round($dtrs->sum("grosspay.regular_holidays.overtime"), 2),
            ],
            "special_holidays" => [
                "regular" => round($dtrs->sum("grosspay.special_holidays.reg_hrs"), 2),
                "overtime" => round($dtrs->sum("grosspay.special_holidays.overtime"), 2),
            ]
        ]);
        $totalGrossPay = round($grossPays->values()->sum("regular") + $grossPays->values()->sum("overtime"), 2);
        $totalSalaryDeduction = $this->getTotalSalaryDeduction($result["salary_deduction"]);
        $totalNetPay = $totalGrossPay - $totalSalaryDeduction;
        $result["total_gross_pay"] = round($totalGrossPay, 2);
        $result["total_salary_deduction"] = round($totalSalaryDeduction, 2);
        $result["total_net_pay"] = round($totalNetPay, 2);
        $result["hours_worked"] = $totalHoursWorked;
        $result["gross_pays"] = $grossPays;
        return $result;
    }
    public function getSalaryDeduction($employee, $filters)
    {
        $salaryGrade = $employee->current_employment?->employee_salarygrade;
        $salary = $salaryGrade ? $salaryGrade->monthly_salary_amount : 0;

        $result = [
            "sss" => $filters["deduct_sss"] ? $employee->sss_deduction($salary, $filters["payroll_type"]) : [],
            "phic" => $filters["deduct_philhealth"] ? $employee->philhealth_deduction($salary, $filters["payroll_type"]) : [],
            "hmdf" => $filters["deduct_pagibig"] ? $employee->pagibig_deduction($salary, $filters["payroll_type"]) : [],
            "ewtc" =>  $employee->with_holding_tax_deduction($salary),
            "loan" => $employee->loan_deduction($salary, $filters["payroll_type"], $filters["payroll_date"]),
            "cash_advance" => $employee->cash_advance_deduction($salary, $filters["payroll_type"], $filters["payroll_date"]),
            "other_deduction" => $employee->other_deduction($salary, $filters["payroll_type"], $filters["payroll_date"]),
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
        if ($deductions["sss"]) {
            $sss = $deductions["sss"]["employee_compensation"] + $deductions["sss"]["total_contribution"];
        }
        if ($deductions["phic"]) {
            $phic = $deductions["phic"]["employee_compensation"];
        }
        if ($deductions["hmdf"]) {
            $hmdf = $deductions["hmdf"]["employee_compensation"];
        }
        if ($deductions["ewtc"]) {
            $ewtc = $deductions["ewtc"];
        }
        // if ($deductions["loan"]) {
        //     $loan = $deductions["loan"];
        // }
        // if ($deductions["cash_advance"]) {
        //     $cashAdvance = $deductions["cash_advance"];
        // }
        return $cashAdvance + $sss + $phic + $hmdf + $ewtc + $loan;
    }
}
