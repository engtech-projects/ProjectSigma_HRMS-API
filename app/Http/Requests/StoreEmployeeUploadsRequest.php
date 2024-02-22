<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeUploadsRequest extends FormRequest
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
            'employee_uploads'=>[
                "required",
                "string",
            ],
            'employee_id'=> [
                "required",
                "integer",
                "exists:employees,id",
            ],
            'upload_type'=>[
                "string",
                "required",
                'in:Documents,Memo'
            ],
            'file'=>[
                "required",
                "max:10000",
                "mimes:application/msword,doc,docx,pdf,zip",
            ],
        ];
    }
}
