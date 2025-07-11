<?php

namespace App\Http\Services;

use App\Models\FailureToLog;

class FailureToLogService
{
    protected $failedLog;
    public function __construct(FailureToLog $failedLog)
    {
        $this->failedLog = $failedLog;
    }
    public function getMyRequests()
    {
        return FailureToLog::with(['employee'])
            ->myRequests()
            ->paginate(config("app.pagination_per_page"));
    }
    public function getMyApprovals()
    {
        return FailureToLog::with(['employee'])
            ->myApprovals()
            ->paginate(config("app.pagination_per_page"));
    }
}
