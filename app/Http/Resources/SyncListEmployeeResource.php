<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SyncListEmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'family_name' => $this->family_name,
            'name_suffix' => $this->name_suffix,
            'nick_name' => $this->nick_name,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'place_of_birth' => $this->place_of_birth,
            'citizenship' => $this->citizenship,
            'blood_type' => $this->blood_type,
            'civil_status' => $this->civil_status,
            'date_of_marriage' => $this->date_of_marriage,
            'telephone_number' => $this->telephone_number,
            'mobile_number' => $this->mobile_number,
            'email' => $this->email,
            'religion' => $this->religion,
            'weight' => $this->weight,
            'height' => $this->height,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
        ];
    }
}
