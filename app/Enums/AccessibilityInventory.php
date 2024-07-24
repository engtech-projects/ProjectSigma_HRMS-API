<?php

namespace App\Enums;

enum AccessibilityInventory: string
{
    case INVENTORY_DASHBOARD = "inventory:dashboard";
    case INVENTORY_SETUP_APPROVALS = "inventory:setup_approvals";
    case INVENTORY_NEW_ITEM_PROFILE = "inventory:new-item-profile";

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
