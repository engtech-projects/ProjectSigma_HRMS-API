<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanDefaultEmployee extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // ...parent::toArray($request),
            "first_name" => $this['first_name'],
            "middle_name" => $this['middle_name'],
            "last_name" => $this['last_name'],
            "suffix" => $this['suffix'],
            "fullname" => $this['fullname'],
            "loan_type" => $this["loan_type"],
            "total_payments" => $this["total_payments"],
        ];
    }
}
