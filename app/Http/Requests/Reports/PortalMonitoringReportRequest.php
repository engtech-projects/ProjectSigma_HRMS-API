<?php

namespace App\Http\Requests\Reports;

use App\Enums\GroupType;
use App\Enums\Reports\PortalMonitoringReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class PortalMonitoringReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'report_type' => [
                "required",
                "string",
                new Enum(PortalMonitoringReport::class)
            ],
            'group_type' => [
                "required",
                "string",
                new Enum(GroupType::class)
            ],
            'department_id' => "nullable|integer",
            'project_id' => "nullable|integer",
            'date_from' => [
                'required_if:report_type,==,' . PortalMonitoringReport::OVERTIME_MONITORING->value,
                'required',
                'date'
            ],
            'date_to' => [
                'required_if:report_type,==,' . PortalMonitoringReport::OVERTIME_MONITORING->value,
                'required',
                'date'
            ]
        ];
    }
}
