<?php

namespace App\Http\Services\Payroll;

use App\Models\Department;
use App\Models\PayrollDetail;
use App\Models\Project;
use App\Models\Request13thMonthDetailAmounts;
use Carbon\Carbon;

class Payroll13thMonthService
{
    protected $request13thMonth;
    protected $payrollsIncluded;

    public function __construct($request13thMonth = null)
    {
        $this->request13thMonth = $request13thMonth;
    }

    public function generateDraft(
        array $employeeIds,
        Carbon|string $dateFrom,
        Carbon|string $dateTo,
        int $advancedDays = 0,
        ?string $chargingType = null,
        ?int $chargingId = null
    ): array {
        // Logic to generate a draft for the 13th month payroll
        // This method should return an array of PayrollDetail objects
        // or any other relevant data structure that represents the draft.
        // Company policy steps for computing the 13th month payroll:
        // 1. Fetch employee's payroll records within the specified date range.
        // 2. Filter out payrolls that are not approved.
        // 3. Get all salary per charging.
        // 4. separated by regular salary, regular holiday salary, special holiday salary.
        // 5. Each Salary is computed by dividing the total salary by 12.
        $this->payrollsIncluded = PayrollDetail::with([
            'payroll_record',
            'charges' => function ($query) {
                $query->whereIn("name", ["Salary Regular Regular", "Salary RegularHoliday Regular", "Salary SpecialHoliday Regular"]);
            },
            "employee.current_employment.employee_salarygrade"
        ])
        ->whereHas('payroll_record', function ($query) use ($dateFrom, $dateTo) {
            $query->where('cutoff_start', '<=', $dateTo)
                ->where('cutoff_end', '>=', $dateFrom)
                ->isApproved();
        })
        ->whereIn('employee_id', $employeeIds)
        ->get();
        $payrollIds = $this->payrollsIncluded->pluck('payroll_record.id')->unique();
        $payrollDetails = $this->payrollsIncluded->groupBy("employee_id")->map(function ($data) use ($advancedDays, $chargingType, $chargingId) {
            $chargeAmounts = $data->flatMap(function ($item) {
                return $item->charges;
            })->groupBy("charging_name")
            ->map(function ($charges) {
                $payrollTotalRegularSalary = $charges->where("name", "Salary Regular Regular")->sum("amount");
                $payrollTotalRegularHolidaySalary = $charges->where("name", "Salary RegularHoliday Regular")->sum("amount");
                $payrollTotalSpecialHolidaySalary = $charges->where("name", "Salary SpecialHoliday Regular")->sum("amount");
                $payrollTotalAmount = $payrollTotalRegularSalary + $payrollTotalRegularHolidaySalary + $payrollTotalSpecialHolidaySalary;
                $result13thMonthRegularSalary = round($payrollTotalRegularSalary / 12, 2);
                $result13thMonthRegularHolidaySalary = round($payrollTotalRegularHolidaySalary / 12, 2);
                $result13thMonthSpecialHolidaySalary = round($payrollTotalSpecialHolidaySalary / 12, 2);
                $result13thMonthTotalAmount = $result13thMonthRegularSalary + $result13thMonthRegularHolidaySalary + $result13thMonthSpecialHolidaySalary;
                return [
                    "charge_type" => $charges->first()->charge_type,
                    "charge_id" => $charges->first()->charge_id,
                    "total_payroll" => $payrollTotalAmount,
                    "amount" => $result13thMonthTotalAmount,
                    "metadata" => [
                        "type" => "Salary",
                        "name" => $charges->first()->charging_name,
                        "payroll_total_amount" => $payrollTotalAmount,
                        "payroll_total_regular_salary" => $payrollTotalRegularSalary,
                        "payroll_total_regular_holiday_salary" => $payrollTotalRegularHolidaySalary,
                        "payroll_total_special_holiday_salary" => $payrollTotalSpecialHolidaySalary,
                        "total_amount" => $result13thMonthTotalAmount,
                        "regular_salary" => $result13thMonthRegularSalary,
                        "regular_holiday_salary" => $result13thMonthRegularHolidaySalary,
                        "special_holiday_salary" => $result13thMonthSpecialHolidaySalary,
                    ],
                ];
            })->values()->all();
            // Add Advance days if applicable
            if ($advancedDays > 0) {
                $employeeDailyRate = $data->first()->employee->current_employment->employee_salarygrade->dailyRate;
                $chargingAmount = $advancedDays * $employeeDailyRate;
                $chargingName = $chargingType === (string) Project::class
                    ? Project::find($chargingId)->project_code
                    : Department::find($chargingId)->department_name;
                $chargeAmounts[] = [
                    "charge_type" => $chargingType,
                    "charge_id" => $chargingId,
                    "total_payroll" => $chargingAmount,
                    "amount" => round($chargingAmount / 12, 2),
                    "metadata" => [
                        "type" => "Advance Days",
                        "name" => $chargingName,
                        "days_advance" => $advancedDays,
                        "daily_rate" => $employeeDailyRate,
                        "payroll_total_amount" => $chargingAmount,
                        "payroll_total_regular_salary" => $chargingAmount,
                        "payroll_total_regular_holiday_salary" => 0,
                        "payroll_total_special_holiday_salary" => 0,
                        "total_amount" => round($chargingAmount / 12, 2),
                        "regular_salary" => round($chargingAmount / 12, 2),
                        "regular_holiday_salary" => 0,
                        "special_holiday_salary" => 0,
                    ],
                ];
            }
            $payrollRecords = $data->pluck('payroll_record.id')->unique();
            $responseData = [
                "employee_id" => $data->first()->employee_id,
                "amounts" => $chargeAmounts,
                "metadata" => [
                    "employee_name" => $data->first()->employee->fullname_last,
                    "payroll_record_ids" => $payrollRecords,
                    "payroll_record_count" => $payrollRecords->count(),
                    "total_payroll_regular" => round(array_sum(array_map(function ($charge) {
                        return $charge['metadata']['payroll_total_regular_salary'] ?? 0;
                    }, $chargeAmounts)), 2),
                    "total_payroll_regular_holiday" => round(array_sum(array_map(function ($charge) {
                        return $charge['metadata']['payroll_total_regular_holiday_salary'] ?? 0;
                    }, $chargeAmounts)), 2),
                    "total_payroll_special_holiday" => round(array_sum(array_map(function ($charge) {
                        return $charge['metadata']['payroll_total_special_holiday_salary'] ?? 0;
                    }, $chargeAmounts)), 2),
                    "charging_names" => implode(", ", array_unique(array_column(array_column($chargeAmounts, 'metadata'), 'name') ?? [])),
                    "total_payroll_amount" => round(array_sum(array_column($chargeAmounts, 'total_payroll')), 2),
                    "total_amount" => round(array_sum(array_column($chargeAmounts, 'amount')), 2),
                ],
            ];
            return $responseData;
        });
        return [
            "details" => $payrollDetails->values()->all(),
            "metadata" => [
                "payroll_record_count" => $payrollIds->count(),
                "payroll_record_ids" => $payrollIds,
            ],
        ];
    }
    public function generateSummary($dateRequested)
    {
        $approvedRequests = Request13thMonthDetailAmounts::with(['detail.request13thMonth'])
        ->whereHas('detail.request13thMonth', function ($query) use ($dateRequested) {
            $query->where('date_requested', $dateRequested)
            ->isApproved();
        })
        ->get()
        ->append(["charging_name"]);
        $groupedData = $approvedRequests->groupBy('charging_name')
        ->map(function ($items, $chargingName) {
            $totalAmount = $items->sum('amount');
            $totalRegularSalary = $items->sum("metadata.payroll_total_regular_salary");
            $totalRegularHolidaySalary = $items->sum("metadata.payroll_total_regular_holiday_salary");
            $totalSpecialHolidaySalary = $items->sum("metadata.payroll_total_special_holiday_salary");
            return [
                'payroll_duration' => $items->first()->detail->request13thMonth->payroll_duration_human,
                'charging_name' => $chargingName,
                'total_amount' => round($totalAmount, 2),
                'total_regular_salary' => round($totalRegularSalary, 2),
                'total_regular_holiday_salary' => round($totalRegularHolidaySalary, 2),
                'total_special_holiday_salary' => round($totalSpecialHolidaySalary, 2),
                'unique_payrolls' => $items->pluck('detail.request13thMonth.metadata.payroll_record_ids')->flatten()->unique()->count(),
            ];
        })->values()->all();
        return $groupedData;
    }
}
