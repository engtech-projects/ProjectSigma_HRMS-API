<?php

namespace App\Enums;

enum AccessibilityInventory: string
{
    case INVENTORY_DASHBOARD = "inventory:dashboard";
    case INVENTORY_SETUP_APPROVALS = "inventory:setup_approvals";
    case INVENTORY_SETUP_ITEMGROUP = "inventory:setup_item group";
    case INVENTORY_SETUP_UNITOFMEASUREMENT = "inventory:setup_unit of measurements";
    case INVENTORY_ITEMPROFILE_BULKUPLOAD = "inventory:item profile_bulk upload";
    case INVENTORY_ITEMPROFILE_NEW_PROFILE = "inventory:item profile_new profile";
    case INVENTORY_ITEMPROFILE_ITEMLIST = "inventory:item profile_item list";
    case INVENTORY_ITEMPROFILE_NEW_ALLREQUESTS = "inventory:item profile_new profile_all request";
    case INVENTORY_ITEMPROFILE_NEW_MYAPPROVALS = "inventory:item profile_new profile_my approvals";
    case INVENTORY_ITEMPROFILE_NEW_FORM = "inventory:item profile_new profile_forms and my requests";
    case INVENTORY_WAREHOUSE_VIEWONLY = "inventory:warehouse_overview_view only";
    case INVENTORY_WAREHOUSE_PSSMANAGER = "inventory:warehouse_overview_pss manager";
    case INVENTORY_REQUESTBOM_NEW_REQUEST = "inventory:request bom_new request";
    case INVENTORY_REQUESTBOM_FORM = "inventory:request bom_forms and my requests";
    case INVENTORY_REQUESTBOM_ALLREQUESTS = "inventory:request bom_list";
    case INVENTORY_REQUESTBOM_MYAPPROVALS = "inventory:request bom_my approvals";
    case INVENTORY_REQUESTBOM_CURRENTBOM = "inventory:request bom_current bom";

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
