<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobApplicantsRequest extends FormRequest
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
            'manpowerrequests_id'=> [
                "nullable",
                "integer",
                "exists:manpower_requests,id",
            ],
            'application_name'=>[
                "nullable",
                "string",
            ],
            'application_letter_attachment'=>[
                "nullable",
                "max:10000",
                "mimes:doc,docx,pdf",
            ],
            'resume_attachment'=>[
                "nullable",
                "max:10000",
                "mimes:doc,docx,pdf",
            ],
            'status'=>[
                "nullable",
                "string",
                'in:Pending,Interviewed,Rejected,Hired'
            ],
        ];
    }
}
