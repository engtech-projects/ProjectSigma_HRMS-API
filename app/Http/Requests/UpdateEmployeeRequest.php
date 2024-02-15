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
                "integer",
                "max:15"
            ],
            'mobile_number'=>[
                "nullable",
                "integer",
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
            'curr_address'=>[
                "nullable",
                "string",
                "max:35"
            ],
            'perm_address'=>[
                "nullable",
                "string",
                "max:35"
            ],
            'father_name'=>[
                "nullable",
                "string",
                "max:35"
            ],
            'mother_name'=>[
                "nullable",
                "string",
                "max:35"
            ],
            'spouse_datebirth'=>[
                "nullable",
                "date",
            ],
            'spouse_occupation'=>[
                "nullable",
                "string",
            ],
            'spouse_contact_no'=>[
                "nullable",
                "integer",
                "min:11",
                "max:11"
            ],
        ];
    }
}
