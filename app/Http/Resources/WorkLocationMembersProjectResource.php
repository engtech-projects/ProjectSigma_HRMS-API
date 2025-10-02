<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkLocationMembersProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "location_information" => [
                "id" => $this->id,
                "name" => $this->project_code,
                "type" => "Project",
            ],
            "employees" => EmployeeSummaryFromInternalResource::collection($this->employeeInternalWorks),
        ];
    }
}
