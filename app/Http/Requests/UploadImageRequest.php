<?php

namespace App\Http\Requests;

use App\Models\Image;
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

    public function prepareForValidation()
    {
        $route = explode('/', $this->route()->uri);
        $prefix = $route[3];
        $imageType = null;
        if ($prefix === Image::PROFILE_IMAGE_TYPE) {
            $imageType = "profile_image";
        } else {
            $imageType = "signature";
        }
        $this->merge([
            "image_type" => $imageType
        ]);
    }
    public function rules(): array
    {
        return [
            'image_file' => [
                'required',
                Rule::when(is_string($this->image_file), new Base64FileValidation, 'mimes:png,jpg')
            ],
            'image_type' => 'string'
        ];
    }
}
