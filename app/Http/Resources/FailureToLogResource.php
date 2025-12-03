<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FailureToLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'date_human' => $this->date_human,
            'time' => $this->time,
            'time_human' => $this->time_human,
            'log_type' => $this->log_type,
            'reason' => $this->reason,
            'charging_type' => $this->charging_type,
            'charging_id' => $this->charging_id,
            'approvals' => ApprovalAttributeResource::collection($this->approvals),
            'charging_designation' => $this->charging_designation,
            "next_approval" => $this->getNextPendingApproval(),
            'employee' => $this->whenLoaded('employee'),
            "created_by_user" => $this->created_by_user_name,
            "created_at_human" => $this->created_at_human,
        ];
        //return parent::toArray($request);
    }
}
