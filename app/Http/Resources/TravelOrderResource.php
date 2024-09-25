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
        $user = $this->when('user', function () {
            return [
                    "name" => $this->user->employee?->fullname_first,
                    "type" => $this->user->type,
            ];
        });

        $employees = $this->whenLoaded('employees', function () {
            $arr = array();
            foreach ($this->employees as $key) {
                array_push($arr, array("id" => $key->id, "name" => $key?->fullname_first));
            }
            return $arr;
        });

        return [
            "id" => $this->id,
            "name" => $this->name,
            "employees" => $employees,
            "requesting_office" => $this->requesting_office,
            "destination" => $this->destination,
            "purpose_of_travel" => $this->purpose_of_travel,
            "date_of_travel" => $this->date_of_travel,
            "date_of_travel_human" => $this->date_of_travel_human,
            "time_of_travel" => $this->time_of_travel,
            "time_of_travel_human" => $this->time_of_travel_human,
            "duration_of_travel" => $this->duration_of_travel,
            "means_of_transportation" => $this->means_of_transportation,
            "remarks" => $this->remarks,
            "approvals" => ApprovalAttributeResource::collection($this->approvals),
            "next_approval" => $this->getNextPendingApproval(),
            "department" => $this->department,
            "requested_by" => $user,
            "request_status" => $this->request_status,
            'charging_designation' => $this->charging_designation,
        ];
    }
}
