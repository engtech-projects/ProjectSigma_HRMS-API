<?php

namespace App\Http\Requests;

use App\Enums\PayrollType;
use App\Enums\ReleaseType;
use App\Http\Traits\HasApprovalValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class GenerateRequestSalaryDisbursementRequest extends FormRequest
{
    use HasApprovalValidation;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
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
            'payroll_date' => 'required|date_format:Y-m-d',
            'payroll_type' => [
                'required',
                'string',
                new Enum(PayrollType::class)
            ],
            'release_type' => [
                'required',
                'string',
                new Enum(ReleaseType::class)
            ],
            ...$this->storeApprovals(),
        ];
    }
}
