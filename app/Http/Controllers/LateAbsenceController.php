<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Services\Report\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class LateAbsenceController extends Controller
{
    public function getLateAbsenceThisMonth()
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

        $reportData = Cache::remember($cacheKey, 1440, function() use ($filter) {
            return ReportService::employeeAbsences($filter);
        });

        $newData = [
            'lates' => collect(),
            'absence' => collect()
        ];

        $reportData->each(function($data) use (&$newData) {
            if ($data["total_lates"] > 0) {
                $newData["lates"]->push($data);
            }
            if ($data["total_absents"] > 0) {
                $newData["absence"]->push($data);
            }
        });

        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetched.",
            "data" => $newData
        ]);
    }
}
