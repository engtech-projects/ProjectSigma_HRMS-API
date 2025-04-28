<?php

namespace Database\Seeders;

use App\Enums\AccessibilityInventory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AccessibilitiesSeederInventory extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // INVENTORY
        DB::table('accessibilities')->upsert(
            [
                [
                    'id' => 2006,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2010,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_DASHBOARD->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2020,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_SETUP_APPROVALS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2030,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_SETUP_ITEMGROUP->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2040,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_SETUP_UNITOFMEASUREMENT->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2050,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_ITEMPROFILE_NEW_ALLREQUESTS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2060,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_ITEMPROFILE_NEW_MYAPPROVALS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2070,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_ITEMPROFILE_NEW_FORM->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2080,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_ITEMPROFILE_ITEMLIST->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2090,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_ITEMPROFILE_NEW_PROFILE->value,
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2095,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_ITEMPROFILE_BULKUPLOAD->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2100,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_WAREHOUSE_VIEWONLY->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2110,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_WAREHOUSE_PSSMANAGER->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2120,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_BOM_FORM->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2130,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_BOM_ALLREQUESTS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2140,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_BOM_MYAPPROVALS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2150,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_BOM_CURRENTBOM->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2160,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_PROCUREMENTSUPPLIERS_ALLREQUEST->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2170,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_PROCUREMENTSUPPLIERS_FORM->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2180,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_PROCUREMENTSPPLIERS_MYAPPROVALS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2190,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_PROCUREMENTSUPPLIERS_EDIT->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2200,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_REQUESTSTOCKS_FORM->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2210,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_REQUESTSTOCKS_ALLREQUESTS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2220,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_REQUESTSTOCKS_MYAPPROVALS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2230,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_WAREHOUSE_MATERIALSRECEIVING_REQUESTPROCESSING->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2240,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_WAREHOUSE_MATERIALSRECEIVING_ALLREQUESTS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2250,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_WAREHOUSE_WITHDRAWAL_FORM->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 1251,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 1252,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 1253,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 1254,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 1255,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 1256,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 1257,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2260,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_WAREHOUSE_WITHDRAWAL_ALLREQUESTS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2270,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_WAREHOUSE_WITHDRAWAL_MYAPPROVALS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2280,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_WAREHOUSE_STOCKTRANSFER_REQUESTPROCESSING->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2290,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_WAREHOUSE_STOCKTRANSFER_ALLREQUESTS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2300,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_PROCUREMENT_PRICEQUOTATION_REQUESTS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2310,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_PROCUREMENT_PRICEQUOTATION_ALLQUOTATIONS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2320,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_PROCUREMENT_PRICEQUOTATION_MYQUOTATIONS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2330,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_PROCUREMENT_CANVASSSUMMARY_REQUESTS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2340,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_PROCUREMENT_CANVASSSUMMARY_ALLREQUEST->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2350,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_PROCUREMENT_CANVASSSUMMARY_MYREQUESTS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2360,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_PROCUREMENT_NOTICEOFCHANGEPURCHASEORDER_FORMSANDMYREQUESTS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2370,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_PROCUREMENT_NOTICEOFCHANGEPURCHASEORDER_ALLREQUEST->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2380,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_PROCUREMENT_NOTICEOFCHANGEPURCHASEORDER_MYREQUESTS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2390,
                    'accessibilities_name' => AccessibilityInventory::INVENTORY_PROCUREMENT_NOTICEOFCHANGEPURCHASEORDER_MYAPPROVALS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],


            ],
            [ "id" ],
            [ "accessibilities_name", "deleted_at"]
        );
    }
}
