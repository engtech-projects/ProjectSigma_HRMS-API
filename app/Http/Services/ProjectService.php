<?php

namespace App\Http\Services;

use App\Exceptions\TransactionFailedException;
use App\Models\Project;

class ProjectService
{
    protected $project;
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function getAll()
    {
        return $this->project->get();
    }

    public function get(Project $project)
    {
        return $project;
    }

    public function create(array $attributes)
    {
        try {
            $this->project->create($attributes);
        } catch (\Exception $e) {
            throw new TransactionFailedException("Create transaction failed.", 500, $e);
        }
    }

    public function update(array $attributes, Project $attendanceLog)
    {
        try {
            $attendanceLog->update($attributes);
        } catch (\Exception $e) {
            throw new TransactionFailedException("Update transaction failed.", 500, $e);
        }
    }

    public function delete(Project $attendanceLog)
    {
        try {
            $attendanceLog->delete();
        } catch (\Exception $e) {
            throw new TransactionFailedException("Delete transaction failed.", 500, $e);
        }
    }
}
