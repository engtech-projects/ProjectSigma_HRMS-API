<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AdministrativeEmployeeTenureship extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $givenDate = Carbon::parse($this->current_employment?->date_from);
        $currentDate = Carbon::now();
        $years = $currentDate->diffInYears($givenDate);
        $months = $currentDate->diffInMonths($givenDate) % 12;

        return [
            "employee_name" => $this['fullname_last'],
            "date_hired" => $this->company_employments?->date_hired,
            "designation" => $this->current_position_name,
            "ternure_ecdc" => $this->company_employments?->date_hired ? $years." Years ". $months." Months" : 0,
            "work_location" => $this->current_employment->work_location,
        ];
    }
}
