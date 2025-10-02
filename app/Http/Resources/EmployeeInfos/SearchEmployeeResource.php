<?php

namespace App\Http\Resources\EmployeeInfos;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchEmployeeResource extends JsonResource
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
            "fullname_first" => $this->fullname_first,
            "fullname_last" => $this->fullname_last,
            "search_details" => $this->fullname_last . ' (' . $this->company_employments->status . ')',
        ];
    }
}
