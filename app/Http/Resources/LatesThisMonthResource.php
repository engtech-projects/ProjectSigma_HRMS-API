<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LatesThisMonthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'employee_id' => $key,
            'fullname_first' => $emp->fullname_first,
            'fullname_last' => $emp->fullname_last,
            'profile_photo' => $emp->profile_photo,
            'lates' => $val
        ];
    }
}
