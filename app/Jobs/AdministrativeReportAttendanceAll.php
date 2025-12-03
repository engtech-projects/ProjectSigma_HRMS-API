<?php

namespace App\Jobs;

use App\Http\Services\Report\ReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class AdministrativeReportAttendanceAll implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $validated;
    /**
     * Create a new job instance.
     */
    public function __construct($validated)
    {
        $this->validated = $validated;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // GENERATE REPORT DATA
        $reportData = ReportService::employeeAbsences($this->validated);
        // Log::info('Generated report data for: ' . $this->uniqueId());
        $cacheKey = $this->validated['report_type'] . '-' . $this->validated['group_type'] . '-' . $this->validated['date_from'] . '-' . $this->validated['date_to'];
        Cache::put($cacheKey, $reportData, now()->addDay());
        // // STORE REPORT DATA IN FILE IN CACHE AND SCHEDULE DELETION
        // $path = 'temp-report-generations/'. $cacheKey . '.json';
        // $storedPath = Storage::disk("public")->put($path, json_encode($reportData));
        // Log::info('Storing report at: ' . $storedPath);
        // DeleteTempFileAfterDelay::dispatch($storedPath)->delay(now()->addDay());
        // cache()->put($cacheKey, $storedPath, now()->addDay());
    }
    public function uniqueId(): string
    {
        return $this->validated['report_type'] . '-' . $this->validated['group_type'] . '-' . $this->validated['date_from'] . '-' . $this->validated['date_to'];
    }
}
