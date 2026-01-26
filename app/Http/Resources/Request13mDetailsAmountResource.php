<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Request13mDetailsAmountResource extends JsonResource
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
            'charge_type'       => $this->charge_type,
            'charge_id'         => $this->charge_id,
            'total_payroll'     => $this->total_payroll,
            'amount'            => $this->amount,
            'metadata'          => $this->metadata,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
