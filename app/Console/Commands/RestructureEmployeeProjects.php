<?php

namespace App\Console\Commands;

use App\Enums\WorkLocation;
use App\Models\Employee;
use App\Models\ProjectMember;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RestructureEmployeeProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:restructure-employee-projects';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $projectMembers = ProjectMember::get()->groupBy('employee_id');
        $employees = Employee::isActive()->get();
        foreach ($employees as $employee) {
            if (isset($projectMembers[$employee->id]) && $employee->current_employment->work_location == WorkLocation::PROJECT->value) {
                $employee->current_employment->projects()->sync($projectMembers[$employee->id]->pluck("project_id"));
            }
        }
    }
}
