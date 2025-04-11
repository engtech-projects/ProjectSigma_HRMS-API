<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Services\Report\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\GenerateLatesAbsencesDashboardReport;

class LateAbsenceController extends Controller
{
    public function getLateAbsenceThisMonth(Request $request)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now();
        $filter = [
            "group_type" => "All",
            "lates-absence" => true,
            "date_from" => $startOfMonth,
            "date_to" => $endOfMonth,
        ];
        $cacheKey = 'employee_late_absences_' . $startOfMonth->format('Y_m') . '_' . $endOfMonth->format('Y_m');

        $reportData = null;
        if(!Cache::has($cacheKey) || ($request->has('reload') && $request->input('reload') === "true")) {

            GenerateLatesAbsencesDashboardReport::dispatch();

            return new JsonResponse([
                "success" => true,
                "message" => "Generating Data in background. Please try again later.",
            ]);
        } else {
            $reportData = Cache::get($cacheKey);
        }

        $newData = [
            'lates' => collect(),
            'absence' => collect()
        ];

        if (count($reportData) > 0) {
            $reportData->each(function($data) use (&$newData) {
                if ($data["total_lates"] > 0) {
                    $newData["lates"]->push($data);
                }
                if ($data["total_absents"] > 0) {
                    $newData["absence"]->push($data);
                }
            });
        }

        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetched.",
            "data" => $newData,
        ]);
    }
}
