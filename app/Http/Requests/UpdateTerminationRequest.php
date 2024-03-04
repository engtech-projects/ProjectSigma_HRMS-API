<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTerminationRequest extends FormRequest
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
            'employee_id'=> [
                "nullable",
                "integer",
                "exists:employees,id",
            ],
            'type_of_termination'=>[
                "nullable",
                'in:voluntary,involuntary'
            ],
            'reason_for_termination'=>[
                "nullable",
                "string"
            ],
            'eligible_for_rehire'=>[
                "nullable",
                "boolean"
            ],
            'last_day_worked'=>[
                "nullable",
                "date"
            ],
        ];
    }
}
