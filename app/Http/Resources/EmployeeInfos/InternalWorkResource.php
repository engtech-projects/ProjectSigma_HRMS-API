<?php

namespace App\Http\Resources\EmployeeInfos;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InternalWorkResource extends JsonResource
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
            "department_name" => $this->department_name,
            "project_names" => $this->project_names->implode(", "),
        ];
    }
}
