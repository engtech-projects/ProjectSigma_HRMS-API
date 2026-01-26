<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectEmployeeResource extends JsonResource
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
            "code" => $this->project_code,
            "project_members_ids" => $this->project_member_ids,
            "project_members" => collect($this->project_has_employees)->map(function ($member) {
                return [
                    "fullname_last" => $member->fullname_last,
                ];
            })
        ];
    }
}
