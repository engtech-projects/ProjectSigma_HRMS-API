<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManpowerRequestRequest extends FormRequest
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
        $this->merge([
            "approvals" => json_decode($this->approvals, true)
        ]);
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
                "required",
                "integer",
                "exists:departments,id",
            ],
            'date_requested' => [
                "required",
                "date",
            ],
            'date_required' => [
                "required",
                "date",
            ],
            'position' => [
                "required",
                "string",
            ],
            'employment_type' => [
                "required",
                "string",
                'in:Student Trainee,Project Hire,Contractual,Regular'
            ],
            'brief_description' => [
                "required",
                "string",
            ],
            'job_description_attachment' => [
                "required",
                "max:10000",
                "mimes:application/msword,doc,docx,pdf,zip",
            ],
            'nature_of_request' => [
                "required",
                "string",
                'in:New/Addition,Replacement'
            ],
            'age_range' => [
                "required",
                "string",
            ],
            'status' => [
                "required",
                "string",
                'in:Single,Married,No Preference'
            ],
            'gender' => [
                "required",
                "string",
                'in:Male,Female,No Preference'
            ],
            'educational_requirement' => [
                "required",
                "string",
            ],
            'preferred_qualifications' => [
                "required",
                "string",
            ],
            'approvals' => [
                "required",
                "array",
            ],
            'approvals.*' => [
                "required",
                "array",
            ],
            'approvals.*.type' => [
                "required",
                "string",
            ],
            'approvals.*.user_id' => [
                "required",
                "integer",
                "exists:users,id",
            ],
            'approvals.*.status' => [
                "required",
                "string",
            ],
            'approvals.*.date_approved' => [
                "nullable",
                "date",
            ],
            'approvals.*.remarks' => [
                "nullable",
                "string",
            ],
            'requested_by' => [
                "required",
                "integer",
                "exists:users,id",
            ],
        ];
    }
}
