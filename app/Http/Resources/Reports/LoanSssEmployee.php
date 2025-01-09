<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanSssEmployee extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "fullname" => $this['fullname_last'],
            "total_payments" => $this["total_payments"],
            "sss_no" => $this['sss_no'],
            "loan_account_no" => "",
        ];
    }
}
