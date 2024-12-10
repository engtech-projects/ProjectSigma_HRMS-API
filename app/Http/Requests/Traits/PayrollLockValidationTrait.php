<?php

namespace App\Http\Requests\Traits;

use App\Enums\AttendanceSettings;
use App\Http\Traits\CheckAccessibility;
use App\Models\Settings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait PayrollLockValidationTrait
{
    use CheckAccessibility;
    // RETURNS TRUE IF PAYROLL IS LOCKED
    public function isPayrollLocked($dateCheck) : bool
    {
        if ($this->checkUserAccess(["ADMIN ONLY"])) { // CHECK FOR ADMIN BYPASS
            return false; // FALSE TO ALLOW ADMIN BYPASS
        }
        $dateCheck = Carbon::parse($dateCheck)->startOfDay();
        $allSettings = Settings::get();
        $pr1Day = $allSettings->where('setting_name', AttendanceSettings::PAYROLL_20TH_LOCKUP_DAY_LIMIT->value)->first()->value;
        $pr1SchedDay = $allSettings->where('setting_name', AttendanceSettings::PAYROLL_20TH_LOCKUP_SCHEDULE_DAY_OF_MONTH->value)->first()->value;
        $pr1SchedTime = $allSettings->where('setting_name', AttendanceSettings::PAYROLL_20TH_LOCKUP_SCHEDULE_TIME_OF_DAY->value)->first()->value;
        $pr2Day = $allSettings->where('setting_name', AttendanceSettings::PAYROLL_5TH_LOCKUP_DAY_LIMIT->value)->first()->value;
        $pr2SchedDay = $allSettings->where('setting_name', AttendanceSettings::PAYROLL_5TH_LOCKUP_SCHEDULE_DAY_OF_MONTH->value)->first()->value;
        $pr2SchedTime = $allSettings->where('setting_name', AttendanceSettings::PAYROLL_5TH_LOCKUP_SCHEDULE_TIME_OF_DAY->value)->first()->value;
        $dateToday = Carbon::now()->startOfDay(); // Used to identify which payroll period is now locked
        $pr1Sched1 = $dateToday->copy()->subMonth()->setDay($pr1SchedDay)->setTimeFromTimeString($pr1SchedTime);
        $pr1Date1 = $dateToday->copy()->subMonth()->setDay($pr1Day);
        $pr2Sched1 = $dateToday->copy()->subMonth()->setDay($pr2SchedDay)->setTimeFromTimeString($pr2SchedTime);
        $pr2Date1 = $dateToday->copy()->subMonth()->setDay($pr2Day);
        $pr1Sched2 = $dateToday->copy()->setDay($pr1SchedDay)->setTimeFromTimeString($pr1SchedTime);
        $pr1Date2 = $dateToday->copy()->setDay($pr1Day);
        $pr2Sched2 = $dateToday->copy()->setDay($pr2SchedDay)->setTimeFromTimeString($pr2SchedTime);
        $pr2Date2 = $dateToday->copy()->setDay($pr2Day);
        $maxDateAllowed = Carbon::now()->endOfMonth(); // the last day of the locked payroll period
        if ($dateToday->gt($pr1Sched1)) {
            $maxDateAllowed = $pr1Date1;
        }
        if ($dateToday->gt($pr2Sched1)) {
            $maxDateAllowed = $pr2Date1;
        }
        if ($dateToday->gt($pr1Sched2)) {
            $maxDateAllowed = $pr1Date2;
        }
        if ($dateToday->gt($pr2Sched2)) {
            $maxDateAllowed = $pr2Date2;
        }
        return $maxDateAllowed->gt($dateCheck);
    }
}
