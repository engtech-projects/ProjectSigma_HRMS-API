<?php

namespace App\Http\Requests;

use App\Enums\VoidRequestModels;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class GetRequestVoidRequest extends FormRequest
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
            "request_type" => [
                "nullable",
                "string",
                new Enum(VoidRequestModels::class)
            ],
            "employee_id" => [
                "nullable",
                "numeric",
                "exists:employees,id"
            ]
        ];
    }
}
