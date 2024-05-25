<?php

namespace App\Http\Requests;

use App\Http\Traits\HasApprovalValidation;
use Illuminate\Foundation\Http\FormRequest;

class StoreManpowerRequestRequest extends FormRequest
{
    use HasApprovalValidation;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->prepareApprovalValidation();
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
            'position_id' => [
                "required",
                "integer",
                "exists:positions,id",
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
            ...$this->storeApprovals(),
        ];
    }
}
