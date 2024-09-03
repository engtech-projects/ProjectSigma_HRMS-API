<?php

namespace App\Enums;

enum AccessibilityInventory: string
{
    case INVENTORY_DASHBOARD = "inventory:dashboard";
    case INVENTORY_SETUP_APPROVALS = "inventory:setup_approvals";
    case INVENTORY_SETUP_ITEMGROUP = "inventory:setup_item group";
    case INVENTORY_SETUP_UNITOFMEASUREMENT = "inventory:setup_unit of measurements";
    case INVENTORY_ITEMPROFILE_NEW_PROFILE = "inventory:item profile_new profile";
    case INVENTORY_ITEMPROFILE_ITEMLIST = "inventory:item profile_list profile";
    case INVENTORY_ITEMPROFILE_NEW_ALLREQUESTS = "inventory:item profile_new profile all request";
    case INVENTORY_ITEMPROFILE_NEW_MYAPPROVALS = "inventory:item profile_new profile my approvals";
    case INVENTORY_ITEMPROFILE_NEW_FORM = "inventory:item profile_new profile forms and my requests";

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
