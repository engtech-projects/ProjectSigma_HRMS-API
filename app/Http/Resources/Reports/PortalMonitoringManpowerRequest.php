<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortalMonitoringManpowerRequest extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

     public function toArray(Request $request): array
     {
         $approvals = $this->summary_approvals;
         return [
             "requesting_department" => $this['department']->department_name,
             "requested_position" => $this['position']->name,
             "employment_type" => $this['employment_type'],
             "nature_of_request" => $this['nature_of_request'],
             "age_range" => $this['age_range'],
             "civil_status" => $this["status"],
             "gender" => $this["gender"],
             "education_requirement" => $this["educational_requirement"],
             "preffered_qualification" => $this["preferred_qualifications"],
             "date_required" => $this->date_required_human,
             "date_requested" => $this->date_requested_human,
             "requested_by" => $this->created_by_user_name,
             "request_status" => $this['request_status'],
             "days_delayed_filling" => $this->days_delayed_filing,
             "date_approved" => $this->date_approved_date_human,
             "approvals" => $approvals
         ];
     }
}
