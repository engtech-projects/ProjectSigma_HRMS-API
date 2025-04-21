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
    case INVENTORY_WAREHOUSE_MATERIALSRECEIVING_REQUESTPROCESSING = "inventory:warehouse_materials receiving_request processing";
    case INVENTORY_WAREHOUSE_WITHDRAWAL_FORM = "inventory:warehouse_withdrawal_form and my requests";
    case INVENTORY_WAREHOUSE_WITHDRAWAL_ALLREQUESTS = "inventory:warehouse_withdrawal_all requests";
    case INVENTORY_WAREHOUSE_WITHDRAWAL_MYAPPROVALS = "inventory:warehouse_withdrawal_my approvals";
    case INVENTORY_WAREHOUSE_MATERIALSRECEIVING_FORM = "inventory:warehouse_materials receiving_form and my requests";
    case INVENTORY_WAREHOUSE_MATERIALSRECEIVING_ALLREQUESTS = "inventory:warehouse_materials receiving_all requests";
    case INVENTORY_WAREHOUSE_STOCKTRANSFER_REQUESTPROCESSING = "inventory:warehouse_stock transfer_request processing";
    case INVENTORY_WAREHOUSE_STOCKTRANSFER_ALLREQUESTS = "inventory:warehouse_stock transfer_all requests";
    case INVENTORY_BOM_FORM = "inventory:bom_form and my requests";
    case INVENTORY_BOM_ALLREQUESTS = "inventory:bom_all requests";
    case INVENTORY_BOM_MYAPPROVALS = "inventory:bom_my approvals";
    case INVENTORY_BOM_CURRENTBOM = "inventory:bom_current bom";

    case INVENTORY_PROCUREMENTSUPPLIERS_ALLREQUEST = "inventory:procurement_suppliers_all requests";
    case INVENTORY_PROCUREMENTSUPPLIERS_FORM = "inventory:procurement_suppliers_form and my requests";
    case INVENTORY_PROCUREMENTSPPLIERS_MYAPPROVALS = "inventory:procurement_suppliers_my approvals";
    case INVENTORY_PROCUREMENTSUPPLIERS_EDIT = "inventory:procurement_suppliers_edit";
    case INVENTORY_REQUESTSTOCKS_FORM = "inventory:request stock_form and my requests";
    case INVENTORY_REQUESTSTOCKS_ALLREQUESTS = "inventory:request stock_all requests";
    case INVENTORY_REQUESTSTOCKS_MYAPPROVALS = "inventory:request stock_my approvals";
    case INVENTORY_PROCUREMENT_PRICEQUOTATIUON_REQUESTS = "inventory:procurement_price quotation_requests";
    case INVENTORY_PROCUREMENT_PRICEQUOTATIUON_ALLQUOTATIONS = "inventory:procurement_price quotation_all quotations";
    case INVENTORY_PROCUREMENT_PRICEQUOTATIUON_MYQUOTATIONS = "inventory:procurement_price quotation_my quotations";
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
