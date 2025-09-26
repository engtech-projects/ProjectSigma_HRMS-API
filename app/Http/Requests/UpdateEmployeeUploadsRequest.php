<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeUploadsRequest extends FormRequest
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
            'employee_uploads' => [
                "nullable",
                "string",
            ],
            'employee_id' => [
                "nullable",
                "integer",
                "exists:employees,id",
            ],
            'upload_type' => [
                "string",
                "nullable",
                'in:Documents,Memo'
            ],
            'file' => [
                "nullable",
                "max:10000",
                "mimes:application/msword,doc,docx,pdf,zip",
            ],
        ];
    }
}
