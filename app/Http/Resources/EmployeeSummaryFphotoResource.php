<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeSummaryFphotoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "first_name" => $this->first_name,
            "middle_name" => $this->middle_name,
            "family_name" => $this->family_name,
            "name_suffix" => $this->name_suffix,
            "nick_name" => $this->nick_name,
            "fullname_first" => $this->fullname_first,
            "fullname_last" => $this->fullname_last,
            "current_position" => $this->current_position_name,
            "profile_photo" => new OriginalImageResource($this->profile_photo),
            "current_department" => $this->current_employment ? $this->current_employment->department_name : null,
        ];
    }
}
