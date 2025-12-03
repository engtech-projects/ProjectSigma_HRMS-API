<?php

namespace App\Enums;

use App\Enums\Traits\EnumHelper;

enum SetupSettingsEnums: string
{
    use EnumHelper;
    // ATTENDANCE SETTINGS
    case EARLY_LOGIN = 'Early login';
    case LATE_LOGOUT = 'Late logout';
    case LATE_ALLOWANCE = 'Late allowance min';
    case LATE_ABSENT = 'Late halfday min';
    // PAYROLL LOCKUP SETTINGS
    case PAYROLL_20TH_LOCKUP_DAY_LIMIT = 'Payroll 20th Lockup Cutoff End';
    case PAYROLL_20TH_LOCKUP_SCHEDULE_DAY_OF_MONTH = 'Payroll 20th Lockup Day Schedule';
    case PAYROLL_20TH_LOCKUP_SCHEDULE_TIME_OF_DAY = 'Payroll 20th Lockup Time Schedule';
    case PAYROLL_5TH_LOCKUP_DAY_LIMIT = 'Payroll 5th Lockup Cutoff End';
    case PAYROLL_5TH_LOCKUP_SCHEDULE_DAY_OF_MONTH = 'Payroll 5th Lockup Day Schedule';
    case PAYROLL_5TH_LOCKUP_SCHEDULE_TIME_OF_DAY = 'Payroll 5th Lockup Time Schedule';
    // SPECIAL ACCESSIBILITY SETTINGS
    case USER_201_EDITOR = 'User 201 editor';
    case USER_SALARY_GRADE_SETTER = 'User salary grade setter';
    // GENERAL ACCOUNT SETTINGS
    case LOGOUT_CHANGE_PASSWORD = 'Logout change password';
    case SINGLE_DEVICE_LOGIN = 'Single device login';
}
