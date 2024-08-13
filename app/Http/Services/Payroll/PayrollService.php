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
        return PayrollRecord::with('payroll_details')->get()->append(['charging_name']);
    }

    public function getMyApprovals()
    {
        $userId = auth()->user()->id;
        $result = PayrollRecord::requestStatusPending()
            ->authUserPending()
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
        } elseif ($type == PayrollType::BI_MONTHLY) {
            return round($amount / 2, 2);
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

}
