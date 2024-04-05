<?php

namespace App\Http\Requests;

use App\Enums\RequestStatusType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTravelOrderRequest extends FormRequest
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
        if (gettype($this->hmo_members) == "string") {
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
                "required",
                "string",
            ],
            'requesting_office' => [
                "required",
                "integer",
                "exists:departments,id",
            ],
            'destination' => [
                "required",
                "string",
            ],
            'purpose_of_travel' => [
                "required",
                "string",
            ],
            'date_and_time_of_travel' => [
                "required",
                "date",
            ],
            'duration_of_travel' => [
                "required",
                "integer",
                "min:1"
            ],
            'means_of_transportation' => [
                "required",
                "string",
            ],
            'remarks' => [
                "required",
                "string",
            ],
            'requested_by' => [
                "required",
                "integer",
                "exists:departments,id",
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
                "required",
                "string",
                new Enum(RequestStatusType::class)
            ],
        ];
    }
}
