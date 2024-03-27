<?php

namespace App\Http\Services;

use App\Models\FailureToLog;
use App\Exceptions\TransactionFailedException;

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
            ->where("created_by", auth()->user()->id)
            ->get();
    }
    public function getMyApprovals()
    {
        $userId = auth()->user()->id;
        $result = FailureToLog::with(['employee'])
            ->requestStatusPending()
            ->authUserPending()
            ->get();
        return $result->filter(function ($item) use ($userId) {
            $nextPendingApproval = $item->getNextPendingApproval();
            return ($nextPendingApproval && $userId === $nextPendingApproval['user_id']);
        });
    }
}
