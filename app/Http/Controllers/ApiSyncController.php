<?php

namespace App\Http\Controllers;

use App\Services\ApiServices\AccountingService;
use App\Services\ApiServices\InventoryService;
use App\Http\Services\ApiServices\ProjectMonitoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiSyncController extends Controller
{
    //
    public function syncAll()
    {
        $authToken = $request->bearerToken();
        DB::transaction(function () use ($authToken) {
            $projectService = new ProjectMonitoringService($authToken);
            // $accountingService = new AccountingService(); // NO SYNCABLE DATA
            // $inventoryService = new InventoryService(); // NO SYNCABLE DATA
            if (!$projectService->syncAll()){
                throw new \Exception("Project monitoring sync failed.");
            }
        });
        return response()->json([
            'message' => 'Successfully synced with api services.',
            'success' => true,
        ]);
    }
    public function syncAllProjectMonitoring()
    {
        $authToken = $request->bearerToken();
        DB::transaction(function () use ($authToken) {
            $projectService = new ProjectMonitoringService($authToken);
            if (!$projectService->syncAll()){
                throw new \Exception("Project monitoring sync failed.");
            }
        });
        return response()->json([
            'message' => 'Successfully synced with Project Monitoring api service.',
            'success' => true,
        ]);
    }
    public function syncProjects()
    {
        $authToken = $request->bearerToken();
        DB::transaction(function () use ($authToken) {
            $projectService = new ProjectMonitoringService($authToken);
            if (!$projectService->syncProjects()){
                throw new \Exception("Project monitoring sync failed.");
            }
        });
        return response()->json([
            'message' => 'Successfully synced all projects.',
            'success' => true,
        ]);
    }
}
