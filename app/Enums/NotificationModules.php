<?php

namespace App\Enums;

enum NotificationModules: string
{
    case ACCOUNTING = "Accounting";
    case HRMS = "Hrms";
    case INVENTORY = "Inventory";
    case PROJECTS = "Projects";

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
