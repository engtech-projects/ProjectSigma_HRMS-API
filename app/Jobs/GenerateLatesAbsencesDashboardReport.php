<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Http\Services\Report\ReportService;
use Illuminate\Support\Facades\Cache;

class GenerateLatesAbsencesDashboardReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
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
        $reportData = ReportService::employeeAbsences($filter);
        Cache::put($cacheKey, $reportData, 1440);
    }
}
