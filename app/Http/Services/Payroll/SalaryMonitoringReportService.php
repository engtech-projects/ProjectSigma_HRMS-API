<?php

namespace App\Http\Services\Payroll;

use App\Models\PayrollDetail;
use App\Models\PayrollDetailsCharging;

class SalaryMonitoringReportService
{
    public const DEPARTMENT = \App\Models\Department::class;
    public const PROJECT = \App\Models\Project::class;

    public static function getPayrollDetails($payrollIds)
    {
        return PayrollDetail::whereIn("payroll_record_id", $payrollIds)
        ->with(['payroll_record', 'otherDeductionPayments.deduction.otherdeduction', 'loanPayments.deduction.loan'])
        ->orderBy("created_at", "DESC")
        ->get()
        ->append([
            'total_basic_pays',
            'total_overtime_pays',
            'total_sunday_pays',
            'total_regular_holiday_pays',
            'total_special_holiday_pays',
            'total_allowance',
            'total_cash_advance_payments',
            'total_loan_payments',
            'total_other_deduction_payments',
        ]);
    }

    public static function formatSalaries($groupedSalaryChargings)
    {
        $returnData = [];
        foreach ($groupedSalaryChargings as $key => $dataCollection) {
            $basic_pay_names = [
                "Salary Regular Regular",
                "Salary Adjustment",
            ];
            $overtime_pay_names = [
                "Salary Rest Regular",
                "Salary RegularHoliday Regular",
                "Salary SpecialHoliday Regular",
                "Salary Regular Overtime",
                "Salary Rest Overtime",
                "Salary RegularHoliday Overtime",
                "Salary SpecialHoliday Overtime",
            ];
            $sunday_pay_names = [
                "Salary Rest Regular",
                "Salary Rest Overtime",
            ];
            $regular_holiday_pay_names = [
                "Salary RegularHoliday Regular",
                "Salary RegularHoliday Overtime",
            ];
            $special_holiday_pay_names = [
                "Salary SpecialHoliday Regular",
                "Salary SpecialHoliday Overtime",
            ];

            $basicSalaries = $dataCollection->filter(function ($chargingItem) use ($basic_pay_names) {
                return in_array($chargingItem['name'], $basic_pay_names);
            });
            $basicSalariesTotalEmployees = collect($basicSalaries)
            ->pluck("payroll_details.employee_id")
            ->unique()
            ->count();

            $overtimeSalaries = $dataCollection->filter(function ($chargingItem) use ($overtime_pay_names) {
                return in_array($chargingItem['name'], $overtime_pay_names);
            });
            $overtimeSalariesTotalEmployees = collect($overtimeSalaries)
            ->pluck("payroll_details.employee_id")
            ->unique()
            ->count();

            $sundaySalaries = $dataCollection->filter(function ($chargingItem) use ($sunday_pay_names) {
                return in_array($chargingItem['name'], $sunday_pay_names);
            });
            $sundaySalariesTotalEmployees = collect($sundaySalaries)
            ->pluck("payroll_details.employee_id")
            ->unique()
            ->count();

            $regularHolidaySalaries = $dataCollection->filter(function ($chargingItem) use ($regular_holiday_pay_names) {
                return in_array($chargingItem['name'], $regular_holiday_pay_names);
            });
            $regularHolidaySalariesTotalEmployees = collect($regularHolidaySalaries)
            ->pluck("payroll_details.employee_id")
            ->unique()
            ->count();

            $specialHolidaySalaries = $dataCollection->filter(function ($chargingItem) use ($special_holiday_pay_names) {
                return in_array($chargingItem['name'], $special_holiday_pay_names);
            });
            $specialHolidayTotalEmployees = collect($specialHolidaySalaries)
            ->pluck("payroll_details.employee_id")
            ->unique()
            ->count();

            $payBasic = round($basicSalaries->sum("amount"), 2);
            $payOvertime = round($overtimeSalaries->sum("amount"), 2);
            $paySunday = round($sundaySalaries->sum("amount"), 2);
            $payRegularHoliday = round($regularHolidaySalaries->sum("amount"), 2);
            $paySpecialHoliday = round($specialHolidaySalaries->sum("amount"), 2);

            $payGross = round($payBasic + $payOvertime, 2);
            $returnData[$key] = [
                "data" => $dataCollection,
                "payroll_record_id" => $dataCollection->first()?->payroll_record_id,
                "project_identifier" => $dataCollection->first()?->project_identifier_name,
                "summary" => [
                    "charge_type" => $dataCollection->first()?->charge_type,
                    "charge" => $key,
                    "charging_pay_basic" => $payBasic,
                    "charging_pay_basic_personnel" => $basicSalariesTotalEmployees,
                    "charging_pay_overtime" => $payOvertime,
                    "charging_pay_overtime_personnel" => $overtimeSalariesTotalEmployees,
                    "charging_pay_sunday" => $paySunday,
                    "charging_pay_sunday_personnel" => $sundaySalariesTotalEmployees,
                    "charging_pay_regular_holiday" => $payRegularHoliday,
                    "charging_pay_regular_personnel" => $regularHolidaySalariesTotalEmployees,
                    "charging_pay_special_holiday" => $paySpecialHoliday,
                    "charging_pay_special_holiday_personnel" => $specialHolidayTotalEmployees,
                ]
            ];
        }
        ksort($returnData);
        return $returnData;
    }

    public static function formatAllowances($groupedAllowances)
    {
        $results = collect($groupedAllowances)->mapWithKeys(function ($sections, $chargingName) {
            $uniqueEmployees = collect($sections)->flatMap(fn ($section) => $section["employee_allowances"])->unique("id");
            // $totalAllowance = $uniqueEmployees->sum(fn($employee) => $employee["pivot"]["allowance_amount"]);
            $totalAllowance = collect($sections)->flatMap(fn ($section) => $section["employee_allowances"])
            ->sum(fn ($allowance) => $allowance["pivot"]["allowance_amount"]);
            $totalEmployees = $uniqueEmployees->count();

            return [
                $chargingName => [
                    // "data" => $sections, //Summary data
                    "payroll_record_id" => null,
                    "project_identifier" => $sections->first()?->project_identifier_name,
                    "summary" => [
                        "charge" => $chargingName,
                        "charging_pay_basic" => 0,
                        "charging_pay_basic_personnel" => 0,
                        "charging_pay_overtime" => 0,
                        "charging_pay_overtime_personnel" => 0,
                        "charging_pay_sunday" => 0,
                        "charging_pay_sunday_personnel" => 0,
                        "charging_pay_regular_holiday" => 0,
                        "charging_pay_regular_personnel" => 0,
                        "charging_pay_special_holiday" => 0,
                        "charging_pay_special_holiday_personnel" => 0,
                        "charging_pay_allowance" => $totalAllowance,
                        "charging_pay_allowance_personnel" => $totalEmployees
                    ]
            ]];
        })->toArray();
        return $results;
    }

    public static function getPayrollSummary($payrollRecordsIds, $allowanceRequest, $withDepartment, $withProject)
    {
        $payrollDetails = self::getPayrollDetails($payrollRecordsIds);
        $payrollDetailsIds = $payrollDetails->pluck("id");
        $uniqueGroup = $payrollDetails->groupBy('payroll_record.charging_name');

        $chargings = PayrollDetailsCharging::whereIn("payroll_details_id", $payrollDetailsIds)
        ->with("payroll_details")
        ->when($withDepartment, function ($query) {
            return $query->where('charge_type', self::DEPARTMENT);
        })
        ->when($withProject, function ($query) {
            return $query->where('charge_type', self::PROJECT);
        })
        ->get()
        ->append(["charging_name"]);

        $uniqueAllowances = $allowanceRequest->groupBy(['charging_name']);
        $uniqueSalaries = $chargings->groupBy(['charging_name']);

        $formattedAllowances = self::formatAllowances($uniqueAllowances);
        $formattedSalaries = self::formatSalaries($uniqueSalaries);

        $allChargings = array_merge($formattedSalaries, $formattedAllowances);

        $allChargingsGroup = [];
        foreach ($allChargings as $key => $value) {
            $allChargingsGroup[] = [
                // 'details' => $uniqueGroup[$key] ?? [],
                'project_name' => $key,
                'project_identifier' => $value["project_identifier"],
                'salaries' => $value["summary"],
            ];
        }

        $newUniqueGroup = collect($allChargingsGroup)
        ->groupBy("project_name")
        ->map(function ($items, $projectName) {
            return [
                "project_name" => $projectName,
                "project_identifier" => $items->first()["project_identifier"],
                "salaries" => collect($items)->reduce(function ($carry, $item) {
                    foreach ($item["salaries"] as $key => $value) {
                        $carry[$key] = isset($carry[$key]) ? $carry[$key] + $value : $value;
                    }
                    return $carry;
                }, [])
            ];
        })->values()->toArray();

        return $newUniqueGroup;
    }
}
