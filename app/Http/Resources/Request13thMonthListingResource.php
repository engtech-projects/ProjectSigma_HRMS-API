<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Request13thMonthListingResource extends JsonResource
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
            'date_requested' => $this->date_requested_human,
            'date_from' => $this->date_from_human,
            'date_to' => $this->date_to_human,
            'days_advance' => $this->days_advance,
            'request_status' => $this->request_status,
            'requested_by' => $this->created_by,
            'created_at_human' => $this->created_at_human
        ];
    }
}
