<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class AdministrativeEmployeeTenureship extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "employee_name" => $this['fullname_last'],
            "date_hired" => $this->company_employments?->employee_date_hired,
            "designation" => $this->current_position_name,
            "ternure_ecdc" => $this->company_employments?->date_hired ? Carbon::parse($this->company_employments?->date_hired)->diffForHumans([
                "syntax" => CarbonInterface::DIFF_ABSOLUTE,
                "parts" => 2,
            ]) : 0,
            "work_location" => $this->current_employment->work_location,
        ];
    }
}
