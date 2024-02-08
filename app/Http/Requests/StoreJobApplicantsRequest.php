<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobApplicantsRequest extends FormRequest
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
                "required",
                "integer",
                "exists:manpower_requests,id",
            ],
            'application_name'=>[
                "required",
                "string",
            ],
            'application_letter_attachment'=>[
                "required",
                "max:10000",
                "mimes:doc,docx,pdf",
            ],
            'resume_attachment'=>[
                "required",
                "max:10000",
                "mimes:doc,docx,pdf",
            ],
            'status'=>[
                "required",
                "string",
                'in:Pending,Interviewed,Rejected,Hired'
            ],
        ];
    }
}
