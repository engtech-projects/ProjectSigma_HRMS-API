<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
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
            //
            'first_name'=> [
                "nullable",
                "string",
            ],
            'middle_name'=>[
                "nullable",
                "string",
            ],
            'family_name'=>[
                "nullable",
                "string",
            ],
            'nick_name'=>[
                "nullable",
                "string",
            ],
            'name_suffix'=>[
                "nullable",
                "string",
            ],
            'gender'=>[
                "nullable",
                "string",
            ],
            'date_of_birth'=>[
                "nullable",
                "date",
            ],
            'place_of_birth'=>[
                "nullable",
                "date",
            ],
            'date_of_marriage'=>[
                "nullable",
                "date",
            ],
            'citizenship'=>[
                "nullable",
                "string",
            ],
            'blood_type'=>[
                "nullable",
                "string",
                "max:55"
            ],
            'civil_status'=>[
                "nullable",
                "string",
                "max:55"
            ],
            'telephone_number'=>[
                "nullable",
                "string",
                "max:15"
            ],
            'mobile_number'=>[
                "nullable",
                "string",
                "min:11",
                "max:11"
            ],
            'email'=>[
                "nullable",
                "string",
                "max:35"
            ],
            'religion'=>[
                "nullable",
                "string",
                "max:35"
            ],
            'weight'=>[
                "nullable",
                "string",
            ],
            'height'=>[
                "nullable",
                "string",
            ],
        ];
    }
}
