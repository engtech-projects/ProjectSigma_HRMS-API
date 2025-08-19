<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'project_code' => $this->project_code,
            'project_members' => $this->whenLoaded('project_has_employees', function ($value) {
                return EmployeeUserResource::collection($value);
            }),
        ];
        //return parent::toArray($request);
    }
}
