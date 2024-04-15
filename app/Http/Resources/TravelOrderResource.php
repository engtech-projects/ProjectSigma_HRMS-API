<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TravelOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->whenLoaded('user', function () {
            return [
                "name" => $this->user->employee?->fullname_last,
                "type" => $this->user->type
            ];
        });
        $employees = $this->whenLoaded('employees', function () {
            return [
                "id" => $this->employee?->id,
                "name" => $this->employee?->fullname_last,
            ];
        });
        return [
            "id" => $this->id,
            "name" => $this->name,
            "employees" => $employees,
            "requesting_office" => $this->requesting_office,
            "destination" => $this->destination,
            "purpose_of_travel" => $this->purpose_of_travel,
            "date_and_time_of_travel" => $this->date_and_time_of_travel,
            "duration_of_travel" => $this->duration_of_travel,
            "means_of_transportation" => $this->means_of_transportation,
            "remarks" => $this->remarks,
            "approvals" => ApprovalAttributeResource::collection($this->approvals),
            "department" => $this->department,
            "requested_by" => $user,
        ];
    }
}
