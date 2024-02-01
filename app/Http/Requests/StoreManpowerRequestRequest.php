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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'requesting_department'=> [
                "required",
                "integer",
            ],
            'date_requested'=>[
                "required",
                "date",
            ],
            'date_required'=>[
                "required",
                "date",
            ],
            'position'=>[
                "required",
                "string",
            ],
            'employment_type'=>[
                "required",
                "string",
                'in:Student Trainee,Project Hire,Contractual,Regular'
            ],
            'brief_description'=>[
                "required",
                "text",
            ],
            'job_description_attachment'=>[
                "required",
                "string",
            ],
            'nature_of_request'=>[
                "required",
                "string",
                'in:Student New/Addition,Replacement'
            ],
            'age_range'=>[
                "required",
                "string",
            ],
            'status'=>[
                "required",
                "string",
                'in:Single, Married,No Preference'
            ],
            'gender'=>[
                "required",
                "string",
                'in:Male, Female, No Preference'
            ],
            'educational_requirement'=>[
                "required",
                "string",
            ],
            'preferred_qualifications'=>[
                "required",
                "text",
            ],
            'approvals'=>[
                "required",
                "array",
            ],
            'approvals.*.label'=>[
                "required",
                "array",
            ],
            'approvals.*.user_id'=>[
                "required",
                "array",
            ],
            'approvals.*.status'=>[
                "required",
                "array",
            ],
            'approvals.*.date_approved'=>[
                "required",
                "array",
            ],
            'remarks'=>[
                "required",
                "text",
            ],
            'request_status'=>[
                "required",
                "string",
                'in:Pending, Approved, Filled, Hold, Cancelled, Disapproved'
            ],
            'charged_to'=>[
                "required",
                "integer",
            ],
            'breakdown_details'=>[
                "required",
                "string",
            ],
        ];
    }
}
