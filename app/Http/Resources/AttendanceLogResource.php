<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceLogResource extends JsonResource
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
            "date" => $this->date,
            "time" => $this->time,
            "log_type" => $this->log_type,
            "attendance_type" => $this->attendance_type,
            "project" => $this->whenLoaded("project"),
            "department" => $this->whenLoaded('department')

        ];
    }
}
