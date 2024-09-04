<?php

namespace App\Enums;

enum AccessibilityAccounting: string
{
    case ACCOUNTING_DASHBOARD = "accounting:dashboard";
    // case ACCOUNTING_ = "accounting:chart of accounts";
    // case ACCOUNTING_ = "accounting:books";
    // case ACCOUNTING_ = "accounting:transaction type";
    // case ACCOUNTING_ = "accounting:document Series";
    // case ACCOUNTING_ = "accounting:posting period";
    // case ACCOUNTING_ = "accounting:account groups";
    // case ACCOUNTING_ = "accounting:stake holder";
    case ACCOUNTING_JOURNALENTRY_VIEWALL = "accounting:journal entry_view all entries";
    case ACCOUNTING_JOURNALENTRY_CREATE = "accounting:journal entry_create";
    case ACCOUNTING_JOURNALENTRY_MODIFY = "accounting:journal entry_modify entry";

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
