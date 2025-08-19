<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleDetailedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "groupType" => $this->groupType,
            "department_id" => $this->department_id,
            "project_id" => $this->project_id,
            "employee_id" => $this->employee_id,
            "scheduleType" => $this->scheduleType,
            "daysOfWeek" => $this->daysOfWeek,
            "startRecur" => $this->startRecur?->format('Y-m-d'),
            "endRecur" => $this->endRecur?->format('Y-m-d'),
            "startTime" => $this->startTime?->format('H:i'),
            "endTime" => $this->endTime?->format('H:i'),
            "day_of_week_names_short" => $this->day_of_week_names_short,
            "day_of_week_names" => $this->day_of_week_names,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "deleted_at" => $this->deleted_at,
            "department" => new SyncListDepartmentResource($this->whenLoaded('department')),
            "employee" => new EmployeeSummaryResource($this->whenLoaded('employee')),
            "project" => new ProjectResource($this->whenLoaded('project')),
            "start_time_human" => $this->start_time_human,
            "end_time_human" => $this->end_time_human,
        ];
    }
}
