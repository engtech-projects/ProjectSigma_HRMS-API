<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceLogType;
use App\Enums\SalaryRequestType;
use App\Http\Resources\CompressedImageResource;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\Schedule;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use App\Http\Services\Report\ReportService;
use Illuminate\Http\JsonResponse;

class AbsentController extends Controller
{
    public function getAbsenceThisMonth(Schedule $req, AttendanceLog $log)
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
