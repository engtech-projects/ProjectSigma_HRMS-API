<?php

namespace App\Enums;

enum AccessibilityAccounting: string
{
    case ACCOUNTING_DASHBOARD = "accounting:dashboard";

    //Accounting Setup
    case ACCOUNTING_SETUP_APPROVALS = "accounting:setup_approvals";
    case ACCOUNTING_SETUP_ACCOUNTS = "accounting:setup_accounts";
    case ACCOUNTING_SETUP_BOOK_OF_ACCOUNTS = "accounting:setup_book of accounts";
    case ACCOUNTING_SETUP_ACCOUNT_GROUPS = "accounting:setup_account groups";
    case ACCOUNTING_SETUP_ACCOUNT_TYPES = "accounting:setup_account types";
    case ACCOUNTING_SETUP_POSTING_PERIODS = "accounting:setup_posting periods";
    case ACCOUNTING_SETUP_CHART_OF_ACCOUNTS = "accounting:setup_chart of accounts";
    case ACCOUNTING_SETUP_STAKEHOLDERS = "accounting:setup_stakeholders";
    case ACCOUNTING_SETUP_SYNCHRONIZATION = "accounting:setup_synchronization";

    //Accounting Request
    case ACCOUNTING_REQUEST_PURCHASE_ORDER = "accounting:request_purchase order";
    case ACCOUNTING_REQUEST_NON_PURCHASE_ORDER = "accounting:request_non purchase order";
    case ACCOUNTING_REQUEST_PRE_PAYROLL_AUDIT = "accounting:request_pre payroll audit";

    //Accounting Voucher
    case ACCOUNTING_VOUCHER_DISBURSEMENT = "accounting:voucher_disbursement";
    case ACCOUNTING_VOUCHER_CASH = "accounting:voucher_cash";

    //Accounting Journal
    case ACCOUNTING_JOURNAL_ENTRY = "accounting:journal_journal entry";

    public static function toArray(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->name] = $case->value;
        }
        return $array;
    }

    public static function toArraySwapped(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->value] = $case->name;
        }
        return $array;
    }
}
