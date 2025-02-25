<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Services\Report\ReportService;
use Illuminate\Http\JsonResponse;
use App\Models\AttendanceLog;
use App\Models\Schedule;

class LateAbsenceController extends Controller
{
    public function getLateAbsenceThisMonth(Schedule $req, AttendanceLog $log)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now();
        $filter = [
            "group_type" => "All",
            "date_from" => $startOfMonth,
            "date_to" => $endOfMonth,
        ];
        $reportData = ReportService::employeeAbsences($filter);
        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetched.",
            "data" => $reportData
        ]);
    }
}
