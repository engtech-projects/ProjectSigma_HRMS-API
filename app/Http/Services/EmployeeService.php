<?php

namespace App\Http\Services;

use App\Helpers;
use App\Http\Traits\Attendance;
use App\Models\Events;
use App\Models\PhilhealthContribution;
use App\Models\SSSContribution;
use Illuminate\Support\Carbon;

class EmployeeService
{
    use Attendance;
    public function employeeDTR($employee, $date)
    {
        $date = Carbon::parse($date);
        $schedules = $employee->schedule_dtr($employee, $date);
        $events = $employee->events_dtr($date);
        $attendances = $employee->attendance_dtr($date);
        $travelOrders = $employee->travel_order_dtr($date);
        $overtime = $employee->overtime_dtr($employee, $date);
        $collection = collect([
            "schedule" => $schedules,
            "events" => $events,
            "attendance" => $attendances,
            "overtime" => $overtime,
            "travel_orders" => $travelOrders
        ]);
        $metaData = $this->getMetaData($collection);
        return [
            "schedule" => $schedules,
            "attendance" => $attendances,
            "travel_order" => $travelOrders,
            "ovetime" => $overtime,
            "leave" => $employee->leave_dtr($date),
            "events" => $events,
            "metadata" => [
                "regular_hrs" => $metaData["regular"]["reg_hrs"], //$attendanceMetadata->totalHours,
                "regular_holiday_hrs" => $metaData["regular"]["reg_holiday_hrs"],
                "special_holiday_hrs" => $metaData["regular"]["spec_holiday_hrs"],
                "rest_day_hrs" => $metaData["regular"]["rest_day_hrs"],
                "regular_overtime_hrs" => $metaData["overtime"]["reg_OT"],  //$overtimeMetadata["reg_OT"],
                "spec_holiday_overtime_hrs" => $metaData["overtime"]["spec_holiday_OT"], //$overtimeMetadata["reg_holiday_OT"],
                "rest_overtime_hrs" => $metaData["overtime"]["rest_day_OT"], //$overtimeMetadata["rest_day_OT"],
                "reg_holiday_overtime" => $metaData["overtime"]["reg_holiday_OT"],

            ]
        ];
    }

    public function generatePayroll(array $period, $employee)
    {
        $salary = $employee->current_employment->employee_salarygrade->monthly_salary_amount;
        $dtr = collect($period)->groupBy(function ($period) {
            return $period["date"];
        })->map(function ($period) use ($employee) {
            $date = $period[0]["date"];
            $dtr = $this->employeeDTR($employee, $date);
            $dtr["gross"] = [];
            return $dtr;
        });

        return [
            "dtr" => $dtr,
            "monthly_salary" => $salary,
            "salary_deduction" => $this->getSalaryDeduction($employee),
        ];
    }

    public function getSalaryDeduction($employee)
    {
        $salary = $employee->current_employment->employee_salarygrade->monthly_salary_amount;
        $cashAdvance = $employee->cash_advance_payroll;
        $sssDeduction = SSSContribution::getContribution($salary);
        $phic = PhilhealthContribution::getContribution($salary);
        return [
            "cash_advance" => $cashAdvance, //$ss
            "sss" => $sssDeduction,
            "sss_loan" => [],
            "phic" => $phic,
            "hmdf" => [],
            "hmdf_loan" => [],
            "mp2" => [],
            "ewtc" => [],
            "coop_loan" => []

        ];
    }
}
