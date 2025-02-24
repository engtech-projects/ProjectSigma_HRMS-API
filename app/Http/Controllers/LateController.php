<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceLogType;
use App\Enums\AttendanceSettings;
use App\Enums\SalaryRequestType;
use App\Http\Resources\CompressedImageResource;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\Schedule;
use App\Models\Settings;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use App\Http\Services\Report\ReportService;
use Illuminate\Support\Facades\Cache;

class LateController extends Controller
{
    public function getLateThisMonth(Schedule $req, AttendanceLog $log)
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
