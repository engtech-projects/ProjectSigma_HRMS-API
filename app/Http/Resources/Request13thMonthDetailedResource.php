<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Request13thMonthDetailedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'employee'         => new EmployeeSummaryResource($this->whenLoaded('employee')),
            'requested_amount' => $this->requested_amount,
            'status'           => $this->status,
            // 'details'          => Request13thMonthDetailsResource::collection($this->whenLoaded('details')),
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}
