<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Request13mDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            "employee_id"       => $this->employee_id,
            'employee'          => new EmployeeSummaryResource($this->employee),
            'amounts'           => Request13mDetailsAmountResource::collection($this->amounts),
            'charging_names'    => implode(', ', $this->amounts->pluck('charging_name')->unique()->toArray()),
            "metadata"          => is_string($this->metadata) ? json_decode($this->metadata, true) : $this->metadata,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
