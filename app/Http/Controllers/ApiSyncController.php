<?php

namespace App\Http\Controllers;

use App\Http\Services\ApiServices\ProjectMonitoringSecretKeyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiSyncController extends Controller
{
    public function syncAll(Request $request)
    {
        DB::transaction(function () {
            // $accountingService = new AccountingSecretKeyService(); // NO SYNCABLE DATA
            // $inventoryService = new InventorySecretKeyService(); // NO SYNCABLE DATA
            $projectService = new ProjectMonitoringSecretKeyService();
            $errorServices = [];
            // if (!$accountingService->syncAll()) {
            //     $errorServices[] = "Accounting";
            // }
            // if (!$inventoryService->syncAll()) {
            //     $errorServices[] = "Inventory";
            // }
            if (!$projectService->syncAll()) {
                $errorServices[] = "Project Monitoring";
            }
            if (!empty($errorServices)) {
                throw new \Exception('Sync with ' . implode(', ', $errorServices) .' failed while trying to sync with all API Services');
            }
        });
        return response()->json([
            'message' => 'Successfully synced with all API services.',
            'success' => true,
        ]);
    }
    // ACCOUNTING
    public function syncAllAccounting(Request $request)
    {
        return response()->json([
            'message' => 'No Services to sync with yet.',
            'success' => true,
        ], 202);
    }
    // INVENTORY
    public function syncAllInventory(Request $request)
    {
        return response()->json([
            'message' => 'No Services to sync with yet.',
            'success' => true,
        ], 202);
    }
    // PROJECT MONITORING
    public function syncAllProjectMonitoring(Request $request)
    {
        DB::transaction(function () {
            $projectService = new ProjectMonitoringSecretKeyService();
            if (!$projectService->syncAll()) {
                throw new \Exception("Project monitoring sync failed.");
            }
        });
        return response()->json([
            'message' => 'Successfully synced with Project Monitoring api service.',
            'success' => true,
        ]);
    }
    public function syncProjects(Request $request)
    {
        DB::transaction(function () {
            $projectService = new ProjectMonitoringSecretKeyService();
            if (!$projectService->syncProjects()) {
                throw new \Exception("Project monitoring sync failed.");
            }
        });
        return response()->json([
            'message' => 'Successfully synced all projects.',
            'success' => true,
        ]);
    }
}
