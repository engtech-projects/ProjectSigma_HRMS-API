<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkLocationMembersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (isset($this->employees)) { // ASSIGNED EMPLOYEES
            return [
                "location_information" => [
                    "id" => $this->id,
                    "name" => $this->department_name ?? $this->project_code,
                    "type" => $this->department_name ? "Department" : "Project",
                ],
                "employees" => $this->employees
            ];
        } else { // UNASSIGNED EMPLOYEES
            return [
                "location_information" => [
                    "name" => "UNASSIGNED",
                ],
                "employees" => parent::toArray($request),
            ];
        }
    }
}
