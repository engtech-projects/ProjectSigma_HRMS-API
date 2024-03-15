<?php

namespace App\Http\Requests;

use App\Enums\ManpowerRequestStatus;
use Illuminate\Foundation\Http\FormRequest;

class UpdateManpowerRequestApprovalRequest extends FormRequest
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
        if ($this->remarks != null) {
            return [
                'remarks' => 'required'
            ];
        }

        return [
            'remarks' => 'nullable'
        ];
    }
}
