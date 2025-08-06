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
            'code' => $this->code,
            'project_members' => $this->whenLoaded('project_has_employees', function ($value) {
                return EmployeeUserResource::collection($value);
            }),
        ];
        //return parent::toArray($request);
    }
}
