<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserAccessibilitiesRequest extends FormRequest
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
            'user_id'=>[
                "nullable",
                "integer",
                "exists:users,id",
                Rule::unique("user_accessibilities","user_id")->ignore($this->route("user_accessibility"),'id')->whereNull('deleted_at')
            ],
            'options'=>"nullable|array|exists:accessibilities,id",
        ];
    }
}
