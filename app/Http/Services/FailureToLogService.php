<?php

namespace App\Http\Services;

use App\Models\FailureToLog;
use App\Exceptions\TransactionFailedException;

class FailureToLogService
{
    protected $failLog;
    public function __construct(FailureToLog $failLog)
    {
        $this->failLog = $failLog;
    }

    public function getAll()
    {
        return $this->failLog->with(['employee'])->get();
    }

    public function get(FailureToLog $failLog)
    {
        return $failLog;
    }

    public function create(array $attributes)
    {
        try {
            $this->failLog->create($attributes);
        } catch (\Exception $e) {
            throw new TransactionFailedException("Create transaction failed.", 500, $e);
        }
    }

    public function update(array $attributes, FailureToLog $failLog)
    {
        try {
            $failLog->update($attributes);
        } catch (\Exception $e) {
            throw new TransactionFailedException("Update transaction failed.", 500, $e);
        }
    }

    public function delete(FailureToLog $failLog)
    {
        try {
            $failLog->delete();
        } catch (\Exception $e) {
            throw new TransactionFailedException("Delete transaction failed.", 500, $e);
        }
    }
}
