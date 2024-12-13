<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkLocationMembersDepartmentResource extends JsonResource
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
                "name" => $this->department_name,
                "type" => "Department",
            ],
            "employees" => EmployeeSummaryResource::collection($this->employees),
        ];
    }
}
