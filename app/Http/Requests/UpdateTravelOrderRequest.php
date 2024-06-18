<?php

namespace App\Http\Requests;

use App\Enums\RequestStatusType;
use App\Enums\StringRequestApprovalStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Http\Traits\HasApprovalValidation;

class UpdateTravelOrderRequest extends FormRequest
{
    use HasApprovalValidation;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if (gettype($this->approvals) == "string") {
            $this->merge([
                "approvals" => json_decode($this->approvals, true)
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => [
                "nullable",
                "string",
            ],
            'requesting_office' => [
                "nullable",
                "integer",
                "exists:departments,id",
            ],
            'destination' => [
                "nullable",
                "string",
            ],
            'purpose_of_travel' => [
                "nullable",
                "string",
            ],
            'date_and_time_of_travel' => [
                "nullable",
                "date",
            ],
            'duration_of_travel' => [
                "nullable",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'means_of_transportation' => [
                "nullable",
                "string",
            ],
            'remarks' => [
                "nullable",
                "string",
            ],
            'employee_ids' => [
                "nullable",
                "array",
            ],
            'employee_ids.*' => [
                "required",
                "integer",
                "exists:employees,id",
            ],
            'request_status' => [
                "nullable",
                "string",
                new Enum(StringRequestApprovalStatus::class)
            ],
        ];
        return array_merge($rules, $this->storeApprovals());
    }
}
