<?php

namespace App\Http\Requests;

use App\Enums\RequestStatusType;
use App\Enums\StringRequestApprovalStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTravelOrderRequest extends FormRequest
{
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
        return [
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
                "integer",
                "min:1"
            ],
            'means_of_transportation' => [
                "nullable",
                "string",
            ],
            'remarks' => [
                "nullable",
                "string",
            ],
            'approvals' => [
                "nullable",
                "array",
            ],
            'approvals.*' => [
                "nullable",
                "array",
            ],
            'approvals.*.type' => [
                "nullable",
                "string",
            ],
            'approvals.*.user_id' => [
                "nullable",
                "integer",
                "exists:users,id",
            ],
            'approvals.*.status' => [
                "nullable",
                "string",
            ],
            'approvals.*.date_approved' => [
                "nullable",
                "date",
            ],
            'approvals.*.remarks' => [
                "nullable",
                "string",
            ],
            'request_status' => [
                "nullable",
                "string",
                new Enum(StringRequestApprovalStatus::class)
            ],
        ];
    }
}
