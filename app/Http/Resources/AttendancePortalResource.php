<?php

namespace App\Http\Resources;

use App\Enums\AssignTypes;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendancePortalResource extends JsonResource
{
    public const DEPARTMENT = "App\Models\Department";
    public const PROJECT = "App\Models\Project";

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name_location" => $this->name_location,
            "ip_address" => $this->ip_address,
            "project_names" => $this->project_names,
            "department_names" => $this->department_names,
            "projects" => $this->projects,
            "departments" => $this->departments,
            "project_names" => $this->project_names,
            "department_names" => $this->department_names,
            "assignment_count" => $this->assignment_count,
            // LEGACY
            "assignment_type" => $this->assignment_type,
            "assignment_id" => $this->assignment_id,
            "assignment" => $this->assignment,
            // "name" => $this->name, // OLD
            // "type" => $this->type, // OLD
            "type" => $this->type ? $this->type : "Multiple/New",
            "name" => $this->name ? $this->name : $this->department_project_names,
        ];
    }
}
