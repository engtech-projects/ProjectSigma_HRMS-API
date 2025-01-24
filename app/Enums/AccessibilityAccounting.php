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
    case ACCOUNTING_SETUP_PARTICULAR_GROUP = "accounting:setup_particular group";
    case ACCOUNTING_SETUP_TERMS = "accounting:setup_terms";
    //Accounting Request
    case ACCOUNTING_REQUEST_PURCHASE_ORDER = "accounting:request_purchase order";
    case ACCOUNTING_REQUEST_NON_PURCHASE_ORDER = "accounting:request_non purchase order";
    case ACCOUNTING_REQUEST_PRE_PAYROLL_AUDIT = "accounting:request_pre payroll audit";

    //Accounting Voucher
    case ACCOUNTING_VOUCHER_DISBURSEMENT = "accounting:voucher_disbursement";
    case ACCOUNTING_VOUCHER_CASH = "accounting:voucher_cash";

    //Accounting Journal
    case ACCOUNTING_JOURNAL_ENTRY = "accounting:journal_journal entry";
    case ACCOUNTING_JOURNAL_ENTRY_CASH_ENTRIES = "accounting:journal_journal entry_list_cash entries";
    case ACCOUNTING_JOURNAL_ENTRY_DISBURSEMENT_ENTRIES = "accounting:journal_journal entry_list_disbursement entries";
    case ACCOUNTING_JOURNAL_ENTRY_FOR_PAYMENT_ENTRIES = "accounting:journal_journal entry_list_for payement entries";

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
