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
        $salaryDeduction = new PayrollDeduction($employee, $salary, $filters);
        $result = [
            "cash_advance" => $salaryDeduction->cashAdvance->cashAdvance,
            "sss" => $filters["deduct_sss"] ? $salaryDeduction->sss : [],
            "phic" => $filters["deduct_philhealth"] ? $salaryDeduction->philhealth : [],
            "hmdf" => $filters["deduct_pagibig"] ? $salaryDeduction->pagibig : [],
            "ewtc" =>  $salaryDeduction->withHoldingTax,
            "loan" => $salaryDeduction->loan->loan
        ];

        return $result;
    }
    public function getTotalSalaryDeduction($deductions)
    {
        $cashAdvance = $deductions["cash_advance"];
        $sss =  $deductions["sss"]["total_compensation"] + $deductions["sss"]["total_contribution"];
        $phic = $deductions["phic"]["total_compensation"];
        $hmdf = $deductions["hmdf"]["total_compensation"];

        return $cashAdvance + $sss + $phic + $hmdf + $deductions["ewtc"] + $deductions["loan"];
    }
}
