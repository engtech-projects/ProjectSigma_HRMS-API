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
    case ACCOUNTING_REQUEST_NON_PURCHASE_ORDER_ALL = "accounting:request_npo-list_all request";
    case ACCOUNTING_REQUEST_NON_PURCHASE_ORDER_MY_REQUEST = "accounting:request_npo-list_my request";
    case ACCOUNTING_REQUEST_NON_PURCHASE_ORDER_MY_APPROVAL = "accounting:request_npo-list_my approval";
    case ACCOUNTING_REQUEST_PRE_PAYROLL_AUDIT = "accounting:request_pre payroll audit";

    //Accounting Voucher
    case ACCOUNTING_VOUCHER_DISBURSEMENT = "accounting:voucher_disbursement";
    case ACCOUNTING_VOUCHER_DISBURSEMENT_ALL = "accounting:voucher_disbursement-list_all request";
    case ACCOUNTING_VOUCHER_DISBURSEMENT_MY_REQUEST = "accounting:voucher_disbursement-list_my request";
    case ACCOUNTING_VOUCHER_DISBURSEMENT_MY_APPROVAL = "accounting:voucher_disbursement-list_my approval";
    case ACCOUNTING_VOUCHER_DISBURSEMENT_FOR_DISBURSEMENT_VOUCHER = "accounting:voucher_disbursement-list_for disbursement voucher";
    case ACCOUNTING_VOUCHER_CASH = "accounting:voucher_cash";
    case ACCOUNTING_VOUCHER_CASH_ALL = "accounting:voucher_cash-list_all request";
    case ACCOUNTING_VOUCHER_CASH_MY_REQUEST = "accounting:voucher_cash-list_my request";
    case ACCOUNTING_VOUCHER_CASH_MY_APPROVAL = "accounting:voucher_cash-list_my approval";
    case ACCOUNTING_VOUCHER_CASH_FOR_CASH_VOUCHER = "accounting:voucher_cash-list_for cash voucher";
    case ACCOUNTING_VOUCHER_CASH_CLEARED = "accounting:voucher_cash-list_cleared list";
    case ACCOUNTING_VOUCHER_CASH_FOR_CLEARING = "accounting:voucher_cash-list_clearing list";

    //Accounting Journal
    case ACCOUNTING_JOURNAL_ENTRY = "accounting:journal_journal entry";
    case ACCOUNTING_JOURNAL_ENTRY_CASH_ENTRIES = "accounting:journal_list_journal entry cash entries";
    case ACCOUNTING_JOURNAL_ENTRY_DISBURSEMENT_ENTRIES = "accounting:journal_list_journal entry disbursement entries";
    case ACCOUNTING_JOURNAL_ENTRY_FOR_PAYMENT_ENTRIES = "accounting:journal_list_journal entry for payement entries";

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
