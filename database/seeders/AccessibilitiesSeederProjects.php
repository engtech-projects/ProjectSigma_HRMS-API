<?php

namespace Database\Seeders;

use App\Enums\AccessibilityProjects;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AccessibilitiesSeederProjects extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // PROJECT MONITORING
        DB::table('accessibilities')->upsert(
            [
                [
                    'id' => 3000,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3001,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3002,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3003,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3004,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3005,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3006,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3007,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3008,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3009,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3010,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_DASHBOARD->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3020,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_PROJECT->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3030,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_MARKETING->value,
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3031,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_MARKETING_MYPROJECTS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3032,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_MARKETING_BIDDINGLIST->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3033,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_MARKETING_PROPOSALLIST->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3034,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_MARKETING_ARCHIVEDLIST->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3035,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_MARKETING_ONHOLDLIST->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3036,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_MARKETING_AWARDEDLIST->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3037,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_MARKETING_DRAFTLIST->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3038,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_MARKETING_PROJECTCATALOGLIST->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3039,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_MARKETING_BILL_OF_QUANTITIES->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3040,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_MARKETING_SUMMARY_OF_RATES->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3041,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_MARKETING_SUMMARY_OF_BID->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3042,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_MARKETING_CASHFLOW->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3043,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_MARKETING_ATTACHMENT->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3050,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_TSS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3051,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_TSS_LIVE_PROJECTS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3052,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_TSS_BILLS_OF_MATERIALS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3053,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_TSS_DUPA->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3054,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_TSS_CASHFLOW->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3055,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_TSS_PROJECT_DETAILS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3060,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_SETUP_APPROVALS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3061,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_SETUP_SYNCHRONIZATION->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3062,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_SETUP_POSITION->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3070,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3080,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3080,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3090,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ],
            [ "id" ],
            [ "accessibilities_name", "deleted_at"]
        );
    }
}
