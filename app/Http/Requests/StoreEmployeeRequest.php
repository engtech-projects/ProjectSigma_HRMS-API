<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
                "required",
                "string",
            ],
            'middle_name'=>[
                "required",
                "string",
            ],
            'family_name'=>[
                "required",
                "string",
            ],
            'name_suffix'=>[
                "required",
                "string",
            ],
            'gender'=>[
                "required",
                "string",
            ],
            'date_of_birth'=>[
                "required",
                "date",
            ],
            'place_of_birth'=>[
                "required",
                "date",
            ],
            'citizenship'=>[
                "required",
                "string",
            ],
            'blood_type'=>[
                "required",
                "string",
                "max:55"
            ],
            'civil_status'=>[
                "required",
                "string",
                "max:55"
            ],
            'telephone_number'=>[
                "required",
                "string",
                "max:15"
            ],
            'mobile_number'=>[
                "required",
                "string",
                "min:11",
                "max:11"
            ],
            'email'=>[
                "required",
                "string",
                "max:35"
            ],
            'religion'=>[
                "required",
                "string",
                "max:35"
            ],
            'curr_address'=>[
                "required",
                "string",
                "max:35"
            ],
            'perm_address'=>[
                "required",
                "string",
                "max:35"
            ],
            'father_name'=>[
                "required",
                "string",
                "max:35"
            ],
            'mother_name'=>[
                "required",
                "string",
                "max:35"
            ],
            'spouse_datebirth'=>[
                "required",
                "date",
            ],
            'spouse_occupation'=>[
                "required",
                "string",
            ],
            'spouse_contact_no'=>[
                "required",
                "string",
                "min:11",
                "max:11"
            ],
        ];
    }
}
