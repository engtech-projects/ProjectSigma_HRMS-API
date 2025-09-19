<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateManpowerRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
        ;
    }

    protected function prepareForValidation()
    {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'requesting_department' => [
                "nullable",
                "integer",
                "exists:departments,id",
            ],
            'date_requested' => [
                "nullable",
                "date",
            ],
            'date_required' => [
                "nullable",
                "date",
            ],
            'position_id' => [
                "required",
                "integer",
                "exists:positions,id"
            ],
            'employment_type' => [
                "nullable",
                "string",
                'in:Student Trainee,Project Hire,Contractual,Regular'
            ],
            'brief_description' => [
                "nullable",
                "string",
            ],
            'job_description_attachment' => [
                "nullable",
                "max:10000",
                "mimes:application/msword,doc,docx,pdf,zip",
            ],
            'nature_of_request' => [
                "nullable",
                "string",
                'in:New/Addition,Replacement'
            ],
            'age_range' => [
                "nullable",
                "string",
            ],
            'status' => [
                "nullable",
                "string",
                'in:Single,Married,No Preference'
            ],
            'gender' => [
                "nullable",
                "string",
                'in:Male,Female,No Preference'
            ],
            'educational_requirement' => [
                "nullable",
                "string",
                "max:250"
            ],
            'preferred_qualifications' => [
                "nullable",
                "string",
            ],
            'request_status' => [
                "nullable",
                "string",
            ],
            'charged_to' => [
                "nullable",
                "integer",
                "exists:departments,id",
            ],
            'breakdown_details' => [
                "nullable",
                "string",
            ],
        ];
    }
}
