<?php

namespace App\Enums;

enum AttendanceSettings: string
{
    case LATE_ALLOWANCE = 'Late allowance min';
    case LATE_ABSENT = 'Late halfday min';
    case PAYROLL_20TH_LOCKUP_DAY_LIMIT = 'Payroll 20th Lockup Cutoff Period End'; // 28 - (12)
    case PAYROLL_20TH_LOCKUP_SCHEDULE_DAY_OF_MONTH = 'Payroll 20th Lockup Schedule'; // 13
    case PAYROLL_20TH_LOCKUP_SCHEDULE_TIME_OF_DAY = 'Payroll 20th Lockup Day'; // 10:00:00
    case PAYROLL_5TH_LOCKUP_DAY_LIMIT = 'Payroll 5th Lockup Cutoff Period End'; // 13 - (27)
    case PAYROLL_5TH_LOCKUP_SCHEDULE_DAY_OF_MONTH = 'Payroll 5th Lockup Schedule'; // 28
    case PAYROLL_5TH_LOCKUP_SCHEDULE_TIME_OF_DAY = 'Payroll 5th Lockup Day'; // 10:00:00
}
