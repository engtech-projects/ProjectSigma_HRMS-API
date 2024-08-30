<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OtherDeductionPaymentsResource extends JsonResource
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
            "date_paid_human" => $this->date_paid_human,
            "otherdeduction" => $this->whenLoaded("otherdeduction"),
            "employee" => $this->whenLoaded("employee", new EmployeeSummaryResource($this->employee)),
        ];
    }
}
