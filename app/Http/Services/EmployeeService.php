<?php

namespace App\Http\Services;

use App\Enums\AssignTypes;
use App\Models\AttendanceLog;
use Illuminate\Support\Carbon;
use App\Http\Traits\Attendance;
use App\Http\Services\Payroll\PayrollDeduction;
use App\Models\SSSContribution;

class EmployeeService
{
    CONST OVERTIME = "Overtime";
    CONST DEPARTMENT = "Department";
    CONST PROJECT = "Project";
    CONST SPECIALHOLIDAY = "Special Holiday";

    public function employeeDTR($employee, $date)
    {
        $schedules_attendances = $employee->applied_schedule_with_attendance($date);
        $events = $employee->events_dtr($date);
        $travelOrders = $employee->travel_order_dtr($date);
        $overtime = $employee->applied_overtime_with_attendance($date);

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
            "overtime" => $overtime,
            "leave" => $leave,
            "events" => $events,
            "metadata" => $employee->getMetaData($collection, $date),
            "daily_charge" => $employee->getCharging($collection, $date),
        ];
    }

    public function generatePayroll(array $period, array $filters, $employee)
    {

        $dtr = collect($period)->groupBy(function ($period) use ($filters) {
            return $period["date"];
        })->map(function ($period) use ($employee, $filters) {
            $date = $period[0]["date"];
            $dtr = $this->employeeDTR($employee, $date);
            $grossPay =  $employee->salary_gross_pay($dtr["metadata"]);
            switch (strtolower($filters["group_type"])) {
                case strtolower(AssignTypes::DEPARTMENT->value):
                    $getId = $filters["department_id"];
                    $main_designation = $employee->get_designation(null, $filters["department_id"]);
                    break;
                case strtolower(AssignTypes::PROJECT->value):
                    $main_designation = $employee->get_designation(null, $filters["department_id"]);
                    $getId = $filters["project_id"];
                    break;
            }
            $chargePay =  $employee->salary_charge_pay($dtr["daily_charge"], $getId);
            $dtr["grosspay"] = $grossPay;
            $dtr["chargepay"] = $chargePay;
            $dtr["main_designation"] = $main_designation;
            return $dtr;
        });

        $tavelcharge = collect();
        $leavecharge = collect();
        $overtime = collect();
        $spcholidaycharge = collect();
        $regularholiday = collect();
        $projects = collect();
        $departments = collect();
        $adjustments = [];

        if(isset($filters["adjustments"])){
            $adjustments = collect($filters["adjustments"])->where("employee_id",$employee->id)->groupBy("employee_id")->map(function($data, $index){
                return[
                    "employee_id" => $index,
                    "adjustment_name" => $data[0]["adjustment_name"],
                    "adjustment_amount" => round($data->sum('adjustment_amount'), 2),
                ];
            });
        }

        $dtrs = $dtr->values();

        $result = [
            "dtr" => $dtr,
            "salary_deduction" => $this->getSalaryDeduction($employee, $filters),
        ];

        switch (strtolower($filters["group_type"])) {
            case strtolower(AssignTypes::DEPARTMENT->value):
                $getId = $filters["department_id"];
                break;
            case strtolower(AssignTypes::PROJECT->value):
                $getId = $filters["project_id"];
                break;
        }

        foreach ($dtr as $data) {
            $dtrChargeLeave = $data["daily_charge"]["leaves"];
            $dtrChargeTavel = $data["daily_charge"]["travels"];
            $dtrChargeSpcHoliday = $data["daily_charge"]["special_holiday"];
            $departments->push($this->getChargeAmount($data["daily_charge"]["departments"], $data["chargepay"]["departments"], EmployeeService::DEPARTMENT, $employee));
            $projects->push($this->getChargeAmount($data["daily_charge"]["projects"], $data["chargepay"]["projects"], EmployeeService::PROJECT, $employee));

            if(count($dtrChargeLeave) > 0){
                $getPay = $data["chargepay"]["leaves"]->where("id", $getId)->first()["amount"];
                $leavecharge->push([
                    "type" => $filters["group_type"],
                    "designation" => $data["main_designation"],
                    "id" => $getId,
                    "amount" => $getPay,
                    "reg_hrs" => round($dtrChargeLeave->sum("reg_hrs"), 2),
                ]);
            }
            if(count($dtrChargeTavel) > 0){
                $getPay = $data["chargepay"]["travels"]->where("id", $getId)->first()["amount"];
                $tavelcharge->push([
                    "type" => $filters["group_type"],
                    "designation" => $data["main_designation"],
                    "id" => $getId,
                    "amount" => $getPay,
                    "reg_hrs" => round($dtrChargeTavel->sum("reg_hrs"), 2),
                ]);
            }
            if(count($dtrChargeSpcHoliday) > 0){
                if(count($data["chargepay"]["special_holiday"]) > 0){
                    $getPay = $data["chargepay"]["special_holiday"]->where("id", $getId)->first()["amount"];
                    $spcholidaycharge->push([
                        "type" => $filters["group_type"],
                        "designation" => $data["main_designation"],
                        "id" => $getId,
                        "amount" => $getPay,
                        "reg_hrs" => round($dtrChargeSpcHoliday->sum("reg_hrs"), 2),
                    ]);
                }
            }
        }
        $overtime->push($this->getChargingOvertime($projects, EmployeeService::PROJECT));
        $overtime->push($this->getChargingOvertime($departments, EmployeeService::DEPARTMENT));

        $departments = $this->getTotalChargeAmount($departments);
        $projects = $this->getTotalChargeAmount($projects);
        $tavelcharge = $this->getTotalChargeAmount($tavelcharge);
        $spcholidaycharge = $this->getTotalChargeAmount($spcholidaycharge);
        $leavecharge = $this->getTotalChargeAmount($leavecharge);
        $overtime = $this->getTotalChargeAmount($overtime);
        $pagibig = $this->getBenefitsCharge($data["chargepay"]["pagibig"], $getId, $filters["group_type"], $employee);
        $philhealth = $this->getBenefitsCharge($data["chargepay"]["philhealth"], $getId, $filters["group_type"], $employee);
        $sss = $this->getBenefitsCharge($data["chargepay"]["sss"], $getId, $filters["group_type"], $employee);

        $charging_salary = collect();
        $charging_salary = $this->appendCollection($overtime, $charging_salary, EmployeeService::OVERTIME);
        $charging_salary = $this->appendCollection($projects, $charging_salary, EmployeeService::PROJECT);
        $charging_salary = $this->appendCollection($departments, $charging_salary, EmployeeService::DEPARTMENT);
        $charging_salary = $this->appendCollection($spcholidaycharge, $charging_salary, EmployeeService::SPECIALHOLIDAY);

        $chargings = [
            "leaves" => $leavecharge,
            "travels" => $tavelcharge,
            "projects" => $projects,
            "departments" => $departments,
            "special_holiday" => $spcholidaycharge,
            "pagibig" => $pagibig,
            "sss" => $sss,
            "philhealth" => $philhealth,
            "salary" => $charging_salary,
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

        $collectAdjustments = collect();
        foreach($adjustments as $key){
            $collectAdjustments->push((object)[
                "employee_id" => $key["employee_id"],
                "adjustment_name" => $key["adjustment_name"],
                "adjustment_amount" => $key["adjustment_amount"],
            ]);
        }

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
            ],
            "adjustments" => $collectAdjustments,
            "chargings" => $chargings,
        ]);

        $totalGrossPay = round($grossPays->values()->sum("regular") + $adjustments->sum('adjustment_amount') + $grossPays->values()->sum("overtime"), 2);
        $totalSalaryDeduction = $this->getTotalSalaryDeduction($result["salary_deduction"]);
        $totalNetPay = $totalGrossPay - $totalSalaryDeduction;
        $result["total_gross_pay"] = round($totalGrossPay, 2);
        $result["total_salary_deduction"] = round($totalSalaryDeduction, 2);
        $result["total_net_pay"] = round($totalNetPay, 2);
        $result["hours_worked"] = $totalHoursWorked;
        $result["gross_pays"] = $grossPays;
        return $result;
    }

    function getChargingOvertime($collection, $type){
        return $collection->groupBy("id")->map(function($data, $index) use ($type){
            if($index){
                return [
                    "id" => $index,
                    "type" => $type,
                    "designation" => $data[0]["designation"],
                    "amt" => round($data->sum('amount_overtime'), 2),
                    "reg_hrs" => round($data->sum('overtime'), 2),
                ];
            }
        })->filter(function($data){
            return $data!=null;
        });
    }

    public function appendCollection($collection, $maincollection, $type){
        foreach($collection as $key){
            $maincollection->push((object)[
                "id" => $key["id"],
                "name" => $type,
                "designation" => $key["designation"],
                "amt" => $key["amt"],
                "reg_hrs" => $key["reg_hrs"],
            ]);
        }
        return $maincollection;
    }

    function getBenefitsCharge($charge, $id, $type, $employee) {
        switch ($type) {
            case EmployeeService::DEPARTMENT:
                $designation = $employee->get_designation(null, $id);
                break;
            case EmployeeService::PROJECT:
                $designation = $employee->get_designation($id, null);
                break;
        }
        return [
            "id" => $id,
            "designation" => $designation,
            "type" =>$type,
            "employer_maximum_contribution" => $charge["employer_maximum_contribution"] ? $charge["employer_maximum_contribution"] : $charge["employer_contribution"],
            "employer_compensation" => $charge["employer_compensation"] ? $charge["employer_compensation"] : $charge["employer_share"],
        ];
    }

    function getTotalChargeAmount($charge){
        return $charge->groupBy("id")->map(function($data, $index) {
            if($index){
                return [
                    "id" => $index,
                    "designation" => $data[0]["designation"],
                    "amt" => round($data->sum('amount'), 2),
                    "reg_hrs" => round($data->sum('reg_hrs'), 2),
                ];
            }
        })->filter(function($data){
            return $data!=null;
        });
    }

    function getChargeAmount($charge, $data, $type, $employee){
        if(count($charge) > 0){
            return $charge->map(function($item) use($data, $type, $employee) {
                if($item["id"]){
                    $getCharge = $data->where("id", $item["id"])->sum('amount');
                    $getChargeOvertime = $data->where("id", $item["id"])->sum('amount_overtime');
                    switch ($type) {
                        case EmployeeService::DEPARTMENT:
                            $designation = $employee->get_designation(null, $item["id"]);
                            break;

                        case EmployeeService::PROJECT:
                            $designation = $employee->get_designation($item["id"], null);
                            break;
                    }
                    return [
                        "id" => $item["id"],
                        "designation" => $designation ? $designation : "" ,
                        "amount" => $getCharge,
                        "amount_overtime" => $getCharge,
                        "amount_regular_holidays_hrs" => $getCharge,
                        "regular_holidays_ot_hrs" => $getCharge,
                        "reg_hrs" => $item['reg_hrs'],
                        "overtime" => $getChargeOvertime,
                        "late" => $item['late'],
                        "undertime" => $item['undertime'],
                    ];
                }
            })[0];
        }
        return;
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
            "other_deductions" => $employee->other_deductions($salary, $filters["payroll_type"], $filters["payroll_date"]),
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
            $sss = $deductions["sss"]["employee_compensation"] + $deductions["sss"]["employee_contribution"];
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
