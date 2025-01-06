<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class EmployeeTenureshipResource extends JsonResource
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
        return [
            // ...parent::toArray($request),
            "employee_name" => $this['fullname_last'],
            "date_hired" => $this->current_employment?->date_from,
            "designation" => $this->current_position_name,
            "ternure_ecdc" => $this->current_employment?->date_from ? $givenDate->diffInMonths($currentDate) : 0,
        ];
    }
}
