<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OvertimeResource extends JsonResource
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
                    "name" => $this->user->employee?->fullname_last,
                    "type" => $this->user->type,
            ];
        });

        $employees = $this->whenLoaded('overtimeEmployees', function () {
            $arr = array();
            foreach ($this->employees as $key) {
                array_push($arr, array("id"=>$key->fullname_last, "name" => $key?->fullname_last));
            }
            return $arr;
        });

        return [
            "id" => $this->id,
            "employees" => $employees,
            "overtime_date" => $this->overtime_date->format('F j, Y'),
            "overtime_start_time" => $this->overtime_start_time,
            "overtime_end_time" => $this->overtime_end_time,
            "reason" => $this->reason,
            "project" => $this->project,
            "department" => $this->department,
            "approvals" => ApprovalAttributeResource::collection($this->approvals),
            "prepared_by" => $user,
            "request_status" => $this->request_status,
        ];
    }
}
