<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTerminationRequest extends FormRequest
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
                "required",
                "integer",
                "exists:employees,id",
            ],
            'type_of_termination'=>[
                "required",
                'in:voluntary,involuntary'
            ],
            'reason_for_termination'=>[
                "required",
                "string"
            ],
            'eligible_for_rehire'=>[
                "required",
                "boolean"
            ],
            'last_day_worked'=>[
                "required",
                "date"
            ],
        ];
    }
}
