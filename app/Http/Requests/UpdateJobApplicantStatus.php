<?php

namespace App\Http\Requests;

use App\Enums\JobApplicationStatusEnums;
use App\Enums\HiringStatuses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateJobApplicantStatus extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if (gettype($this->processing_checklist) == "string") {
            $this->merge([
                "processing_checklist" => json_decode($this->processing_checklist, true)
            ]);
        }
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'hiring_status' => [
                "nullable",
                "string",
                new Enum(HiringStatuses::class),
            ],
            'remarks' => [
                "nullable",
                "string",
            ],
            'processing_checklist' => [
                "nullable",
                "array",
            ],
        ];
    }
}
