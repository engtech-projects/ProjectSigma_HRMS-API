<?php

namespace App\Enums;

enum AccessibilityInventory: string
{
    case INVENTORY_DASHBOARD = "inventory:dashboard";
    case INVENTORY_SETUP_APPROVALS = "inventory:setup_approvals";
    // case INVENTORY_SETUP_UNITOFMEASUREMENT = "inventory:setup_unit of measurements";
    case INVENTORY_SETUP_ITEMGROUP = "inventory:setup_item group";
    case INVENTORY_ITEMPROFILE_NEWPROFILE = "inventory:item profile_new profile";

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
