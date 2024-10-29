<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeDetailedEnumResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            "fullname_first" => $this->fullname_first,
            "fullname_last" => $this->fullname_last,
            "department" => $this->current_employment?->employee_department,
            "project" => $this->employee_has_projects?->last(),
        ];
    }
}
