<?php

namespace App\Http\Services;

use App\Enums\AssignTypes;
use App\Enums\SalaryRequestType;
use App\Http\Services\Payroll\PayrollService;
use App\Models\Department;
use App\Models\Project;

class EmployeeService
{
    public const OVERTIME = "Overtime";
    public const DEPARTMENT = "Department";
    public const PROJECT = "Project";
    public const SPECIALHOLIDAY = "Special Holiday";

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
            "travel_order" => array_values($travelOrders->toArray()),
            "overtime" => $overtime,
            "leave" => $leave,
            "events" => $events,
            "metadata" => $employee->getMetaData($collection, $date),
        ];
    }

    public function generatePayroll(array $period, array $filters, $employee)
    {
        $payrollCharging = [
            "id" => 8,  // Default HR Department (temporary static data, need to be variable/env)
            "type" => Department::class,
            "charging_name" => Department::find(8)->department_name,
        ];
        $salary = 0;
        // Setting Payroll Request Project/Department Charging
        switch (strtolower($filters["group_type"])) {
            case strtolower(AssignTypes::DEPARTMENT->value):
                $payrollCharging["id"] = $filters["department_id"];
                $payrollCharging["type"] = Department::class;
                $payrollCharging["charging_name"] = Department::find($filters["department_id"])->department_name;
                break;
            case strtolower(AssignTypes::PROJECT->value):
                $payrollCharging["id"] = $filters["project_id"];
                $payrollCharging["type"] = Project::class;
                $payrollCharging["charging_name"] = Project::find($filters["project_id"])->project_code;
                break;
        }
        // Getting Employee DTR and Gross Income
        $dtr = collect($period)->groupBy(function ($period) use ($filters) {
            return $period["date"];
        })->map(function ($period) use ($employee, $filters) {
            $date = $period[0]["date"];
            $dtr = $this->employeeDTR($employee, $date);
            $grossPay =  $employee->salary_gross_pay($dtr["metadata"]);
            $dtr["grosspay"] = $grossPay;
            return $dtr;
        });
        $dtrValues = $dtr->values();
        $totalHoursWorked = $this->aggregateTotalHoursWorked($dtrValues);
        $grossSalaries = collect([...$this->aggregateTotalGrossPays($dtrValues)]);
        $fixedSalary = PayrollService::getPayrollTypeValue($filters["payroll_type"], $employee->current_employment->employee_salarygrade->monthly_salary_amount);
        if($employee->current_employment->salary_type == SalaryRequestType::SALARY_TYPE_FIXED_RATE->value) {
            $salary = $fixedSalary;
        } else {
            $salary = round($grossSalaries->values()->sum("regular")  + $grossSalaries->values()->sum("overtime"), 2);
        }
        // Getting Employee Adjustments from Payroll Request
        $adjustments = [];
        if(isset($filters["adjustments"])) {
            $adjustments = $this->collectEmployeeAdjustments($filters["adjustments"], $employee->id);
        }
        // Group Together Incomes
        $grossPays = collect([
            ...$grossSalaries,
            ...["adjustments" => $adjustments],
        ]);
        // Get Salary Deductions
        $salaryDeductions = $this->getSalaryDeduction($employee, $filters);
        // Get Chargings
        $chargings = [
            ...$this->aggregateAdjustmentCharging($adjustments, $payrollCharging), // Adjustments
            ...$this->aggregateSalaryDeductionEmployersCharging($salaryDeductions, $payrollCharging), // Employer Deductions (SSS, Philhealth, Pagibig)
        ];
        // Salary Chargings
        if($employee->current_employment->salary_type == SalaryRequestType::SALARY_TYPE_FIXED_RATE->value) {
            $chargings = collect([
                ...$chargings,
                [
                    "name" => "Salary Regular Regular",
                    "charging_name" => $payrollCharging["charging_name"],
                    "charge_type" => $payrollCharging["type"],
                    "charge_id" => $payrollCharging["id"],
                    "amount" => $fixedSalary
                ]
            ]);
        } else {
            $chargings = collect([
                ...$this->aggregateDTRCharging($dtrValues, $employee->current_employment->employee_salarygrade->dailyRate),
                ...$chargings,
            ]);
        }
        $result["dtr"] = $dtr;
        $result["adjustments"] = $adjustments;
        $result["gross_pays"] = $grossPays;
        $result["salary_deduction"] = $salaryDeductions;
        $result["hours_worked"] = $totalHoursWorked;
        $totalAdjustment =  $adjustments->sum('adjustment_amount');
        $totalGrossPay = $salary + $totalAdjustment;
        $totalSalaryDeduction = $this->getTotalSalaryDeduction($salaryDeductions);
        $totalNetPay = $totalGrossPay - $totalSalaryDeduction;
        $result["chargings"] = $chargings;
        $result["total_gross_pay"] = round($totalGrossPay, 2);
        $result["total_salary_deduction"] = round($totalSalaryDeduction, 2);
        $result["total_net_pay"] = round($totalNetPay, 2);
        return $result;
    }

    public function appendCollection($collection, $maincollection, $type)
    {
        foreach($collection as $key) {
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

    public function collectEmployeeAdjustments($adjustments, $employeeId)
    {
        return collect($adjustments)
        ->where("employee_id", $employeeId)
        // ->groupBy("employee_id")
        ->map(function ($data, $index) {
            return[
                "employee_id" => $index,
                "adjustment_name" => $data["adjustment_name"],
                "adjustment_amount" => $data['adjustment_amount'],
            ];
        });

    }

    public function aggregateTotalHoursWorked($dtrs)
    {
        return [
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
    }

    public function aggregateTotalGrossPays($dtrs)
    {
        return [
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
        ];
    }

    public function aggregateDTRCharging($dtrs, $dailyRate)
    {
        return $dtrs->flatMap(function ($dtr) use ($dailyRate) {
            return [
                ...collect($dtr["metadata"]["charging"]["regular"]["reg_hrs"])->map(function ($dtr2) use ($dailyRate) {
                    return [
                        "name" => "Salary Regular Regular",
                        "charge_type" => $dtr2["model"],
                        "charge_id" => $dtr2["id"],
                        "amount" => PayrollService::getSalaryByRateHour("regular", "reg_hrs", $dailyRate, $dtr2["hrs_worked"])
                    ];
                }),
                ...collect($dtr["metadata"]["charging"]["regular"]["overtime"])->map(function ($dtr2) use ($dailyRate) {
                    return [
                        "name" => "Salary Regular Overtime",
                        "charge_type" => $dtr2["model"],
                        "charge_id" => $dtr2["id"],
                        "amount" => PayrollService::getSalaryByRateHour("regular", "overtime", $dailyRate, $dtr2["hrs_worked"])
                    ];
                }),
                ...collect($dtr["metadata"]["charging"]["rest"]["reg_hrs"])->map(function ($dtr2) use ($dailyRate) {
                    return [
                        "name" => "Salary Rest Regular",
                        "charge_type" => $dtr2["model"],
                        "charge_id" => $dtr2["id"],
                        "amount" => PayrollService::getSalaryByRateHour("rest", "reg_hrs", $dailyRate, $dtr2["hrs_worked"])
                    ];
                }),
                ...collect($dtr["metadata"]["charging"]["rest"]["overtime"])->map(function ($dtr2) use ($dailyRate) {
                    return [
                        "name" => "Salary Rest Overtime",
                        "charge_type" => $dtr2["model"],
                        "charge_id" => $dtr2["id"],
                        "amount" => PayrollService::getSalaryByRateHour("rest", "overtime", $dailyRate, $dtr2["hrs_worked"])
                    ];
                }),
                ...collect($dtr["metadata"]["charging"]["regular_holidays"]["reg_hrs"])->map(function ($dtr2) use ($dailyRate) {
                    return [
                        "name" => "Salary RegularHoliday Regular",
                        "charge_type" => $dtr2["model"],
                        "charge_id" => $dtr2["id"],
                        "amount" => PayrollService::getSalaryByRateHour("regular_holidays", "reg_hrs", $dailyRate, $dtr2["hrs_worked"])
                    ];
                }),
                ...collect($dtr["metadata"]["charging"]["regular_holidays"]["overtime"])->map(function ($dtr2) use ($dailyRate) {
                    return [
                        "name" => "Salary RegularHoliday Overtime",
                        "charge_type" => $dtr2["model"],
                        "charge_id" => $dtr2["id"],
                        "amount" => PayrollService::getSalaryByRateHour("regular_holidays", "overtime", $dailyRate, $dtr2["hrs_worked"])
                    ];
                }),
                ...collect($dtr["metadata"]["charging"]["special_holidays"]["reg_hrs"])->map(function ($dtr2) use ($dailyRate) {
                    return [
                        "name" => "Salary SpecialHoliday Regular",
                        "charge_type" => $dtr2["model"],
                        "charge_id" => $dtr2["id"],
                        "amount" => PayrollService::getSalaryByRateHour("special_holidays", "reg_hrs", $dailyRate, $dtr2["hrs_worked"])
                    ];
                }),
                ...collect($dtr["metadata"]["charging"]["special_holidays"]["overtime"])->map(function ($dtr2) use ($dailyRate) {
                    return [
                        "name" => "Salary SpecialHoliday Overtime",
                        "charge_type" => $dtr2["model"],
                        "charge_id" => $dtr2["id"],
                        "amount" => PayrollService::getSalaryByRateHour("special_holidays", "overtime", $dailyRate, $dtr2["hrs_worked"])
                    ];
                }),
            ];
        })
        ->groupBy(["name", "charge_type", "charge_id"])
        ->flatMap(function ($types, $name) {
            return $types->flatMap(function ($ids, $type) use ($name) {
                return $ids->map(function ($chargings, $id) use ($name, $type) {
                    return [
                        "name" => $name,
                        "charge_type" => $type,
                        "charge_id" => $id,
                        "charging_name" => $type === "App\\Models\\Department" ? Department::find($id)->department_name : Project::find($id)->project_code,
                        "amount" => $chargings->sum("amount"),
                    ];
                });
            });
        });
    }

    public function aggregateAdjustmentCharging($adjustments, $charging)
    {
        $total = collect($adjustments)->sum("adjustment_amount");
        if ($total > 0) {
            return [
                [
                    "name" => "Salary Adjustment", // Adjustment
                    "charge_type" => $charging["type"],
                    "charge_id" => $charging["id"],
                    "charging_name" => $charging["charging_name"],
                    "amount" => collect($adjustments)->sum("adjustment_amount"),
                ],
            ];
        }
        return [];
    }

    public function aggregateSalaryDeductionEmployersCharging($salaryDeductions, $charging)
    {
        return [
            ...$this->prepareSssDeductionCharging($salaryDeductions, $charging),
            ...$this->preparePhilhealthDeductionCharging($salaryDeductions, $charging),
            ...$this->preparePagibigDeductionCharging($salaryDeductions, $charging),
        ];
    }
    public function prepareSssDeductionCharging($salaryDeductions, $charging)
    {
        if ($salaryDeductions["sss"]) {
            return [
                [
                    "name" => "SSS Employer",
                    "charge_type" => $charging["type"],
                    "charge_id" => $charging["id"],
                    "charging_name" => $charging["charging_name"],
                    "amount" => $salaryDeductions["sss"]["employer_compensation"],
                ],
            ];
        }
        return [];
    }
    public function preparePhilhealthDeductionCharging($salaryDeductions, $charging)
    {
        if ($salaryDeductions["phic"]) {
            return [
                [
                    "name" => "Philhealth Employer",
                    "charge_type" => $charging["type"],
                    "charge_id" => $charging["id"],
                    "charging_name" => $charging["charging_name"],
                    "amount" => $salaryDeductions["phic"]["employer_compensation"],
                ],
            ];
        }
        return [];
    }
    public function preparePagibigDeductionCharging($salaryDeductions, $charging)
    {
        if ($salaryDeductions["hmdf"]) {
            return [
                [
                    "name" => "Pagibig Employer",
                    "charge_type" => $charging["type"],
                    "charge_id" => $charging["id"],
                    "charging_name" => $charging["charging_name"],
                    "amount" => $salaryDeductions["hmdf"]["employer_compensation"],
                ],
            ];
        }
        return [];
    }
}
