<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
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
            'leave_name'=> [
                "required",
                "string",
            ],
            'amt_of_leave'=>[
                "required",
                "integer",
            ],
            'employment_type'=>[
                "required",
                "array",
            ],
            'employment_type.data'=>[
                "required",
                "array",
                'in:Probationary,Regular/FullTime,Part Time,Project Based,Contractual'
            ],
        ];
    }
}
