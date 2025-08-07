<?php

namespace App\Enums;

enum SetupSettingsEnums: string
{
    // ATTENDANCE SETTINGS
    case EARLY_LOGIN = 'Early login';
    case LATE_LOGOUT = 'Late logout';
    case LATE_ALLOWANCE = 'Late allowance min';
    case LATE_ABSENT = 'Late halfday min';
    // PAYROLL LOCKUP SETTINGS
    case PAYROLL_20TH_LOCKUP_DAY_LIMIT = 'Payroll 20th Lockup Cutoff Period End'; // 28 - (12)
    case PAYROLL_20TH_LOCKUP_SCHEDULE_DAY_OF_MONTH = 'Payroll 20th Lockup Schedule (Day of Month 1 - 31)'; // 13
    case PAYROLL_20TH_LOCKUP_SCHEDULE_TIME_OF_DAY = 'Payroll 20th Lockup Schedule (Time Of Day [00:00 - 23:59])'; // 10:00:00
    case PAYROLL_5TH_LOCKUP_DAY_LIMIT = 'Payroll 5th Lockup Cutoff Period End'; // 13 - (27)
    case PAYROLL_5TH_LOCKUP_SCHEDULE_DAY_OF_MONTH = 'Payroll 5th Lockup Schedule (Day of Month 1 - 31)'; // 28
    case PAYROLL_5TH_LOCKUP_SCHEDULE_TIME_OF_DAY = 'Payroll 5th Lockup Schedule (Time Of Day [00:00 - 23:59])'; // 10:00:00
    // SPECIAL ACCESSIBILITY SETTINGS
    case USER_201_EDITOR = 'User 201 editor';
    case USER_SALARY_GRADE_SETTER = 'User salary grade setter';
    // GENERAL ACCOUNT SETTINGS
    case LOGOUT_CHANGE_PASSWORD = 'Logout change password';
    case SINGLE_DEVICE_LOGIN = 'Single device login';
}
