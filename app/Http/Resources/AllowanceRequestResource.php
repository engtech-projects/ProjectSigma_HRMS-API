<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllowanceRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "charge_assignment" => $this->charge_assignment,
            "allowance_date" => $this->allowance_date,
            "cutoff_start" => $this->cutoff_start,
            "cutoff_end" => $this->cutoff_end,
            "allowance_date_human" => $this->allowance_date_human,
            "cutoff_start_human" => $this->cutoff_start_human,
            "cutoff_end_human" => $this->cutoff_end_human,
            "total_days" => $this->total_days,
            "employee_allowances" => EmployeeAllowanceResource::collection($this->employee_allowances),
            "request_status" => $this->request_status,
            "approvals" => ApprovalAttributeResource::collection($this->approvals),
            "next_approval" => $this->getNextPendingApproval(),
            "created_by" => $this->created_by,
            "requested_by_user" => $this->created_by_user->employee->fullname_first ?? $this->created_by_user->name,
        ];
    }
}
