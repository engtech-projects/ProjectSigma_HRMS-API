<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkLocationMembersUnassignedResource extends JsonResource
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
                "name" => "UNASSIGNED",
            ],
            "employees" => EmployeeSummaryResource::collection(parent::toArray($request)),
        ];
    }
}
