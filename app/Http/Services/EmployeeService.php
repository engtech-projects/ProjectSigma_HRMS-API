<?php

namespace App\Http\Services;

use App\Models\AttendanceLog;
use Illuminate\Support\Carbon;
use App\Http\Traits\Attendance;
use App\Http\Services\Payroll\PayrollDeduction;

class EmployeeService
{
    public function employeeDTR($employee, $date)
    {
        $schedules_attendances = $employee->applied_schedule_with_attendance($date);
        $schedules = $employee->applied_schedule($date);
        $events = $employee->events_dtr($date);
        $attendances = $employee->applied_schedule_with_attendance($date);
        $travelOrders = $employee->travel_order_dtr($date);
        $overtime = $employee->employee_overtime()->where('overtime_date', $date)->get();

        $leave = $employee->leave_dtr($date);
        $collection = [
            "schedules_attendances" => $schedules_attendances,
            "schedule" => $schedules,
            "events" => $events,
            "attendance" => $attendances,
            "overtime" => $overtime,
            "travel_orders" => $travelOrders,
            "leave" => $leave
        ];
        return [
            "schedules_attendances" => $schedules_attendances,
            "schedule" => $schedules,
            "attendance" => $attendances,
            "travel_order" => $travelOrders,
            "ovetime" => $overtime,
            "leave" => $employee->leave_dtr($date),
            "events" => $events,
            "metadata" => $employee->getMetaData($collection, $date),
        ];
    }

    public function generatePayroll(array $period, array $filters, $employee)
    {
        $dtr = collect($period)->groupBy(function ($period) {
            return $period["date"];
        })->map(function ($period) use ($employee) {
            $date = $period[0]["date"];
            $dtr = $this->employeeDTR($employee, $date);
            $dtr["gross_pay"] =  $employee->salary_gross_pay($dtr["metadata"]);
            return $dtr;
        });

        $result = [
            "dtr" => $dtr,
            "salary_deduction" => $this->getSalaryDeduction($employee, $filters),

        ];

        $totalGross = 0;
        foreach (collect($result) as $res) {
            $total = 0;
            foreach ($res as $value) {
                $regularHrs = $value["gross_pay"]["regular"]["reg_hrs"] + $value["gross_pay"]["rest"]["reg_hrs"] + $value["gross_pay"]["regular_holidays"]["reg_hrs"] + $value["gross_pay"]["special_holidays"]["reg_hrs"];
                $overtime = $value["gross_pay"]["regular"]["overtime"] + $value["gross_pay"]["rest"]["overtime"] + $value["gross_pay"]["regular_holidays"]["overtime"] + $value["gross_pay"]["special_holidays"]["overtime"];
                
            }

        }
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

    public function grossPayPerDay($dtr, $dailyRate)
    {
        $result = [];
        foreach ($dtr as $key => $value) {
            $result[$key]["reg_hrs"] = $value["reg_hrs"] * $dailyRate;
            $result[$key]["overtime"] = $value["overtime"] * $dailyRate;
        }
        return $result;
    }
}
