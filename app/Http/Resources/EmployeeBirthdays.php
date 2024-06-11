<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeBirthdays extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "fullname_first" => $this->fullname_first,
            "fullname_last" => $this->fullname_last,
            "profile_photo" => $this->profile_photo,
            "date_of_birth" => $this->date_of_birth,
            "id" => $this->id,
        ];
    }
}
