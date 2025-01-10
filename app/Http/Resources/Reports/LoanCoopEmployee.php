<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanCoopEmployee extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "first_name" => $this['first_name'],
            "middle_name" => $this['middle_name'],
            "last_name" => $this['last_name'],
            "suffix" => $this['suffix'],
            "fullname" => $this['fullname_last'],
            "loan_type" => $this["loan_type"],
            "total_payments" => $this["total_payments"],
        ];
    }
}
