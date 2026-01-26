<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormatSingleApprovalRequest extends FormRequest
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
            'type' => [
                "required",
                "string",
                "max:200",
            ],
            'user_id' => [
                "nullable",
                "integer",
                "exists:users,id",
                Rule::notIn([1]),
            ],
            'status' => [
                "required",
                "string",
                "max:200",
            ],
            'remarks' => [
                "required",
                "string",
                "max:200",
            ],
            'date_approved' => [
                "required",
                "date",
            ],
            'date_denied' => [
                "required",
                "date",
            ],
        ];
    }
}
