<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AllJobApplicantResource extends JsonResource
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
            "fullname_last" => $this->fullname_last,
            "desired_position" => $this->desired_position ?? "",
            "position" => $this->manpower ? $this->manpower->position->name : "",
            "status" => $this->status ?? "",
            "created_at" => $this->created_at ? Carbon::parse($this->created_at)->format('F j, Y') : "",
            "remarks" => $this->remarks ?? "",
        ];
    }
}
