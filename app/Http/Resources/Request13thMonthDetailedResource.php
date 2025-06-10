<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Request13thMonthDetailedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id ?? '',
            'employee'              => new EmployeeSummaryResource($this->whenLoaded('employee')),
            'details'               => Request13mDetailsResource::collection($this->whenLoaded('details')),
            'date_requested'        => $this->date_requested,
            'date_requested_human'  => $this->date_requested_human,
            'payroll_duration'      => $this->payroll_duration_human,
            'date_from'             => $this->date_from,
            'date_to'               => $this->date_to,
            'days_advance'          => $this->days_advance,
            'metadata'              => $this->metadata,
            'request_status'        => $this->request_status,
            "approvals"             => $this->approvals ? ApprovalAttributeResource::collection($this->approvals) : null,
            "next_approval"         => $this->getNextPendingApproval(),
            "created_at_human"      => $this->created_at_human,
            "created_by_user_name"  => $this->created_by_user_name,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}
