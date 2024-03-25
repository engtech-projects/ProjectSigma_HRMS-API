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

    public function getAll()
    {
        return $this->failedLog->with(['employee'])->get();
    }

    public function get(FailureToLog $failedLog)
    {
        return $failedLog;
    }

    public function create(array $attributes)
    {
        try {
            $this->failedLog->create($attributes);
        } catch (\Exception $e) {
            throw new TransactionFailedException("Create transaction failed.", 500, $e);
        }
    }

    public function update(array $attributes, FailureToLog $failedLog)
    {
        try {
            $failedLog->update($attributes);
        } catch (\Exception $e) {
            throw new TransactionFailedException("Update transaction failed.", 500, $e);
        }
    }

    public function delete(FailureToLog $failedLog)
    {
        try {
            $failedLog->delete();
        } catch (\Exception $e) {
            throw new TransactionFailedException("Delete transaction failed.", 500, $e);
        }
    }
}
