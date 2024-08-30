<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OtherDeductionResource extends JsonResource
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
            "employee" => new EmployeeSummaryResource($this->employee),
            "payments" => OtherDeductionPaymentsResource::collection($this->otherDeductionPayment),
            "date_filed" => $this->created_at_human,
            "deduction_start" => $this->deduction_start_human,
            "is_fully_paid" => $this->cashPaid(),
            "total_paid" => $this->total_paid,
            "remaining_balance" => $this->balance,
        ];
    }
}
