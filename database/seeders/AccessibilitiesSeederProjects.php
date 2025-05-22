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
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3031,
                    'accessibilities_name' => AccessibilityProjects::PROJECT_MONITORING_MARKETING_MY_PROJECTS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3032,
                    'accessibilities_name' => AccessibilityProjects::PROJECT_MONITORING_MARKETING_BIDDING_LIST->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3033,
                    'accessibilities_name' => AccessibilityProjects::PROJECT_MONITORING_MARKETING_PROPOSAL_LIST->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3034,
                    'accessibilities_name' => AccessibilityProjects::PROJECT_MONITORING_MARKETING_ARCHIVED_LIST->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3035,
                    'accessibilities_name' => AccessibilityProjects::PROJECT_MONITORING_MARKETING_ON_HOLD_LIST->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3036,
                    'accessibilities_name' => AccessibilityProjects::PROJECT_MONITORING_MARKETING_AWARDED_LIST->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3037,
                    'accessibilities_name' => AccessibilityProjects::PROJECT_MONITORING_MARKETING_DRAFT_LIST->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3040,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_TSS->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3050,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_SETUP->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3053,
                    'accessibilities_name' => AccessibilityProjects::PROJECTMONITORING_SETUP_SYNCHRONIZATION->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3060,
                    'accessibilities_name' => "",
                    'deleted_at' => Carbon::now(),
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
