<?php

namespace App\Http\Requests;

use App\Enums\GroupType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
class EmployeeTenureshipRequest extends FormRequest
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
            'grouptype' => [
                "required",
                "string",
                new Enum(GroupType::class)
            ],
            'department_id' => "nullable|integer",
            'project_id' => "nullable|integer",
        ];
    }
}
