<?php

namespace App\Http\Services\Payroll;

use App\Enums\PayrollType;
use App\Models\PayrollRecord;

class PayrollService
{
    protected $payrollRequest;
    public function __construct(PayrollRecord $payrollRequest)
    {
        $this->payrollRequest = $payrollRequest;
    }

    public function getAll()
    {
        return PayrollRecord::orderBy("created_at", "DESC")
        ->get();
    }

    public function getMyRequests()
    {
        return PayrollRecord::where("created_by", auth()->user()->id)
        ->orderBy("created_at", "DESC")
        ->get();
    }

    public function getMyApprovals()
    {
        $userId = auth()->user()->id;
        $result = PayrollRecord::requestStatusPending()
            ->authUserPending()
            ->orderBy("created_at", "DESC")
            ->get();
        return $result->filter(function ($item) use ($userId) {
            $nextPendingApproval = $item->getNextPendingApproval();
            return ($nextPendingApproval && $userId === $nextPendingApproval['user_id']);
        });
    }

    public static function getPayrollTypeValue($type, $amount)
    {
        if ($type == PayrollType::WEEKLY->value) {
            return round($amount / 4, 2);
        } elseif ($type == PayrollType::BI_MONTHLY->value) {
            return round($amount / 2, 2);
        }
        // Monthly
        return $amount;
    }

    public static function getPayrollTypeMonthlyValue($type, $amount)
    {
        if ($type == PayrollType::WEEKLY->value) {
            return round($amount * 4, 2); // Weekly to Monthly
        } elseif ($type == PayrollType::BI_MONTHLY->value) {
            return round($amount * 2, 2); // Bimonthly to Monthly
        }
        // Monthly
        return $amount;
    }

    public static function getSalaryByRateHour($dayType = "regular", $salaryType = "reg_hrs", $dailyRate, $hoursWorked)
    {
        $daysWorked = $hoursWorked / 8;
        $salary = 0;
        if ($dayType == "rest") {
            if ($salaryType == "reg_hrs") {
                $salary = $daysWorked * $dailyRate * 1.3;
            } else { // overtime
                $salary = $daysWorked * $dailyRate * 1.6;
            }
        } elseif ($dayType == "regular_holidays") {
            if ($salaryType == "reg_hrs") {
                $salary = $daysWorked * $dailyRate * 1;
            } else { // overtime
                $salary = $daysWorked * $dailyRate * 1.6;
            }
        } elseif ($dayType == "special_holidays") {
            if ($salaryType == "reg_hrs") {
                $salary = $daysWorked * $dailyRate * 1.3;
            } else { // overtime
                $salary = $daysWorked * $dailyRate; // Not in Sample Payroll
            }
        } else { // ($dayType == "regular")
            if ($salaryType == "reg_hrs") {
                $salary = $daysWorked * $dailyRate;
            } else { // overtime
                $salary = $daysWorked * $dailyRate * 1.25;
            }
        }
        return round($salary, 2);
    }
    public static function GeneratePayrollEmployeePayrollPrefetch($employees)
    {
        // PRE FETCH FOR GENERATE PAYROLL
        // WILL SEGGREGATE EMPLOYEE BASED ON EMPLOYMENT STATUS TO AVOID DTR OVERHEAD FOR FIXED RATE EMPLOYEES
        $errors = [];
        $fixedRateEmployees = [];
        $nonFixedRateEmployees = [];
        foreach ($employees as $employee) {
            if (!$employee->current_employment) {
                $errors[] = "Employee ".$employee->fullname_first." is not Employed.";
            }
            if (!$employee->current_employment->position_id) {
                $errors[] = "Employee ".$employee->fullname_first." has no Position Set.";
            }
            if (!$employee->current_employment->employee_salarygrade) {
                $errors[] = "Employee ".$employee->fullname_first." has no Salary Grade Set.";
            }
        }
    }
}
