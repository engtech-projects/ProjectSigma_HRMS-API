<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserCredentialRequest extends FormRequest
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
            'typechange' =>
            [
                "required",
                "string",
                "in:password,name,email"
            ],
            'name' =>
            [
                "nullable",
                "string",
                "max:35",
                'required_if:typechange,==,name',
            ],
            'email' => [
                "nullable",
                "string",
                "max:35",
                'required_if:typechange,==,email',
            ],
            'password' => [
                "nullable",
                "string",
                "max:35",
                'required_if:typechange,==,password',
            ],
            'current_password' => [
                "required",
                "string",
                "max:35",
            ]
        ];
    }
}
