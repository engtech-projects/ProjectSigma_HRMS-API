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
            'date' => $this->date,
            'time' => $this->time,
            'log_type' => $this->log_type,
            'reason' => $this->reason,
            'approvals' => ApprovalAttributeResource::collection($this->approvals),
            'employee' => $this->whenLoaded('employee'),
        ];
        //return parent::toArray($request);
    }
}
