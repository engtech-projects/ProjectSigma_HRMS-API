<?php

namespace App\Http\Requests\Traits;

use App\Enums\AttendanceSettings;
use App\Models\Settings;
use Carbon\Carbon;

trait PayrollLockValidationTrait
{
    public function isPayrollLocked($dateCheck)
    {
        $dateCheck = Carbon::parse($dateCheck);
        $allSettings = Settings::get();
        $pr1Day = $allSettings->where('setting_name', AttendanceSettings::PAYROLL_20TH_LOCKUP_DAY_LIMIT->value)->first()->value;
        $pr1SchedDay = $allSettings->where('setting_name', AttendanceSettings::PAYROLL_20TH_LOCKUP_SCHEDULE_DAY_OF_MONTH->value)->first()->value;
        $pr1SchedTime = $allSettings->where('setting_name', AttendanceSettings::PAYROLL_20TH_LOCKUP_SCHEDULE_TIME_OF_DAY->value)->first()->value;
        $pr2Day = $allSettings->where('setting_name', AttendanceSettings::PAYROLL_5TH_LOCKUP_DAY_LIMIT->value)->first()->value;
        $pr2SchedDay = $allSettings->where('setting_name', AttendanceSettings::PAYROLL_5TH_LOCKUP_SCHEDULE_DAY_OF_MONTH->value)->first()->value;
        $pr2SchedTime = $allSettings->where('setting_name', AttendanceSettings::PAYROLL_5TH_LOCKUP_SCHEDULE_TIME_OF_DAY->value)->first()->value;
        $dateToday = Carbon::now();
        $pr1Sched1 = $dateToday->copy()->subMonth()->setDay($pr1SchedDay)->setTimeFromTimeString($pr1SchedTime);
        $pr1Date1 = $dateToday->copy()->subMonth()->setDay($pr1Day);
        $pr1Sched2 = $dateToday->copy()->setDay($pr1SchedDay)->setTimeFromTimeString($pr1SchedTime);
        $pr1Date2 = $dateToday->copy()->setDay($pr1Day);
        $pr2Sched1 = $dateToday->copy()->subMonth()->setDay($pr2SchedDay)->setTimeFromTimeString($pr2SchedTime);
        $pr2Date1 = $dateToday->copy()->subMonth()->setDay($pr2Day);
        $pr2Sched2 = $dateToday->copy()->setDay($pr2SchedDay)->setTimeFromTimeString($pr2SchedTime);
        $pr2Date2 = $dateToday->copy()->setDay($pr2Day);
        $maxDateAllowed = Carbon::now()->endOfMonth();
        if ($dateToday->lt($pr1Sched1)) {
            $maxDateAllowed = $pr1Date1;
        } else if ($dateToday->lt($pr2Sched1)) {
            $maxDateAllowed = $pr2Date1;
        } else if ($dateToday->lt($pr1Sched2)) {
            $maxDateAllowed = $pr1Date2;
        } else if ($dateToday->lt($pr2Sched2)) {
            $maxDateAllowed = $pr2Date2;
        }
        return $maxDateAllowed->lt($dateCheck);
    }
}
