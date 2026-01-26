<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AbsencesThisMonthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'fullname_first' => $employee->fullname_first,
            'fullname_last' => $employee->fullname_last,
            'profile_photo' => $employee->profile_photo,
            'absent' => $workDaysCount - $attendedDays,
            'workDaysCount' => $workDaysCount,
            'attendDays' => $attendedDays,
        ];
    }
}
