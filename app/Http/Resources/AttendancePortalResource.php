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
        $name = "";
        $type = "";

        if ($this->assignment_type == AttendancePortalResource::DEPARTMENT) {
            $name = $this->assignment->department_name;
            $type = AssignTypes::DEPARTMENT;
        }
        if ($this->assignment_type == AttendancePortalResource::PROJECT) {
            $name = $this->assignment->project_code;
            $type = AssignTypes::PROJECT;
        }

        return [
            "id" => $this->id,
            "name_location" => $this->name_location,
            "ip_address" => $this->ip_address,
            "assignment_type" => $this->assignment_type,
            "assignment_id" => $this->assignment_id,
            "assignment" => $this->assignment,
            "name" => $name,
            "type" => $type,
        ];
    }
}
