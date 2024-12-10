<?php

namespace App\Http\Resources\EmployeeInfos;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompleteDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            "current_employment" => new InternalWorkResource($this->current_employment),
            "employee_internal" => InternalWorkResource::collection($this->employee_internal),
        ];
    }
}
