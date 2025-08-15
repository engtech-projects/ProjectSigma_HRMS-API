<?php

namespace App\Models\Traits;

use App\Models\Department;
use App\Models\Project;

trait SeparatedCharging
{
    public function getChargingNameAttribute()
    {
        if ($this->project_id) {
            return $this->project->project_code;
        }
        if ($this->department_id) {
            return $this->department->department_name;
        }
        return 'No charging found.';
    }
    public function getChargingClassAttribute()
    {
        if ($this->project_id) {
            return Project::class;
        }
        return Department::class;
    }
    public function getChargingIdAttribute()
    {
        if ($this->project_id) {
            return $this->project_id;
        }
        return $this->department_id ?? 4;
    }
}
