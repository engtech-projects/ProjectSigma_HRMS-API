<?php

namespace App\Http\Requests;

use App\Enums\AttendanceLogType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreFailureToLogRequest extends FormRequest
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
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i:s',
            'log_type' => [
                'required',
                'string',
                new Enum(AttendanceLogType::class)
            ],
            'reason' => 'required|string',
            'approvals' => 'required|array',
            'employee_id' => 'required|integer',
        ];
    }
}
