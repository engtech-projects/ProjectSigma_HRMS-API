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
            ...parent::toArray($request),
            "first_name" => $this['employee']['fullname_last'],
            "middle_name" => $this['employee']['fullname_last'],
            "last_name" => $this['employee']['fullname_last'],
            "suffix" => $this['employee']['fullname_last'],
            "fullname" => $this['employee']['fullname_last'],
            "employee_name" => $this['employee']['fullname_last'],
            "loan_type" => $this["loan_type"],
            "total_payments" => $this["total_payments"],
        ];
    }
}
