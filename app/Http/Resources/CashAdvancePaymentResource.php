<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashAdvancePaymentResource extends JsonResource
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
            "cashadvance" => $this->whenLoaded("cashadvance"),
            "employee" => $this->whenLoaded("employee", new EmployeeSummaryResource($this->employee)),
            "amount_paid_formatted" => number_format($this->amount_paid, 2),
        ];
    }
}
