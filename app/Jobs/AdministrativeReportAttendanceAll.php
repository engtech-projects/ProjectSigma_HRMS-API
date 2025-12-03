<?php

namespace App\Jobs;

use App\Http\Services\Report\ReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AdministrativeReportAttendanceAll implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $cacheKey = 'employee-absences-' . $this->validated['group_type'] . '-' . $this->validated['date_from'] . '-' . $this->validated['date_to'];
        $reportData = ReportService::employeeAbsences($this->validated);
        cache()->put($cacheKey, $reportData, now()->addDay());
    }
    public function uniqueId(): string
    {
        return $this->validated['report_type'] . '-' . $this->validated['group_type'] . '-' . $this->validated['date_from'] . '-' . $this->validated['date_to'];
    }
}
