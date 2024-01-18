<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'email' => "nullable|string|max:35",
            'email_verified_at' => "nullable|date_format:Y-m-d H:i:s",
            'password' => "nullable|string|max:255",
            'remember_token' => "nullable|string|max:100",
            'type' => "nullable|in:Administrator,Employee",
            'accessibilities'=>"nullable|array|exists:accessibilities,id",
        ];
    }
}
