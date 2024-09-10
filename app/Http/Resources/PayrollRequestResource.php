<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollRequestResource extends JsonResource
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
            "id" => $this->id,
            "payroll_date_human" => $this->payroll_date_human,
            "charging_name" => $this->charging_name,
            "cutoff_start_human" => $this->cutoff_start_human,
            "cutoff_end_human" => $this->cutoff_end_human,
            "payroll_details" => PayrollDetailsResource::collection($this->payroll_details),
            "approvals" => ApprovalAttributeResource::collection($this->approvals),
            "next_approval" => $this->getNextPendingApproval(),
        ];
    }
}
