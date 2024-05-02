<?php

namespace App\Http\Requests;

use App\Enums\EmploymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreLeaveRequest extends FormRequest
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
        if (gettype($this->employment_status) == "string") {
            $this->merge([
                "employment_status" => json_decode($this->employment_status, true),
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
            'leave_name' => [
                "required",
                "string",
            ],
            'amt_of_leave' => [
                "required",
                "integer",
            ],
            'employment_status' => [
                "required",
                "array",
            ],
            'employment_status.*' => [
                "required",
                "string",
                new Enum(EmploymentType::class)
            ],
        ];
    }
}
