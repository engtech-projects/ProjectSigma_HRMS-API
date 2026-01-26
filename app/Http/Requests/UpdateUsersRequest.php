<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsersRequest extends FormRequest
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
            'name' => "nullable|string|max:35",
            'email' => [
                "nullable",
                "string",
                "max:35",
                Rule::unique("users", "email")->ignore($this->route("user"), 'id')->whereNull('deleted_at'),
            ],
            'password' => "nullable|string|max:255",
            'accessibilities' => "nullable|array|exists:accessibilities,id",
        ];
    }
}
