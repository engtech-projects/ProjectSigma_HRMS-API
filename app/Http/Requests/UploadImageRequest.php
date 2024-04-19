<?php

namespace App\Http\Requests;

use App\Rules\Base64FileValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadImageRequest extends FormRequest
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
            'image_file' => [
                'required',
                Rule::when(is_string($this->image_file), new Base64FileValidation, 'mimes:png,jpg')
            ]
        ];
    }
}
