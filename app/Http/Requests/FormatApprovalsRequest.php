<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormatApprovalsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'approvals' => [
                "nullable",
                "array",
            ],
            'approvals.*.type' => [
                "required",
                "string",
                "max:200",
            ],
            'approvals.*.user_id' => [
                "nullable",
                "integer",
                "exists:users,id",
                Rule::notIn([1]),
            ],
            'approvals.*.status' => [
                "required",
                "string",
                "max:200",
            ],
            'approvals.*.remarks' => [
                "required",
                "string",
                "max:200",
            ],
            'approvals.*.date_approved' => [
                "required",
                "date",
            ],
            'approvals.*.date_denied' => [
                "required",
                "date",
            ],
        ];
    }
}
