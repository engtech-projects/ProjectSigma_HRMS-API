<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeSeminartrainingRequest extends FormRequest
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
                "required`",
                "integer",
                "exists:employees,id",
            ],
            'name_title_training'=>[
                "required`",
                "string",
            ],
            'inclusive_dates'=>[
                "required`",
                "date",
            ],
            'venue'=>[
                "required`",
                "string",
            ],
            'training_provider'=>[
                "required`",
                "string",
            ],
        ];
    }
}
