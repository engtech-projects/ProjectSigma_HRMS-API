<?php

namespace App\Http\Requests;

use App\Http\Traits\HasApprovalValidation;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest13thMonthRequest extends FormRequest
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
        if (gettype($this->employees) == "string") {
            $this->merge([
                "employees" => json_decode($this->employees, true, 512, JSON_THROW_ON_ERROR)
            ]);
        }
        if (gettype($this->metadata) == "string") {
            $this->merge([
                "metadata" => json_decode($this->metadata, true, 512, JSON_THROW_ON_ERROR)
            ]);
        }
        if (gettype($this->details) == "string") {
            $this->merge([
                "details" => json_decode($this->details, true, 512, JSON_THROW_ON_ERROR)
            ]);
        }
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Request fields
            'date_requested' => ['required', 'date_format:Y-m-d'],
            'date_from' => ['required', 'date_format:Y-m-d'],
            'date_to' => ['required', 'date_format:Y-m-d', 'after_or_equal:date_from'],
            'employees' => ['required', 'array'],
            'employees.*' => ['integer', 'exists:employees,id', 'distinct'],
            'days_advance' => ['nullable', 'integer', 'min:0'],
            'charging_type' => ['nullable', 'string'],
            'charging_id' => ['nullable', 'integer'],
            'metadata' => ["required", 'array'],
            "details" => ['required', 'array'],
            "details.*.employee_id" => ['required', 'integer', 'exists:employees,id'],
            "details.*.metadata" => ["required", 'array'],
            "details.*.amounts" => ['required', 'array'],
            "details.*.amounts.*.charge_type" => ['required', 'string'],
            "details.*.amounts.*.charge_id" => ['required', 'integer'],
            "details.*.amounts.*.total_payroll" => ['required', 'numeric', 'min:0'],
            "details.*.amounts.*.amount" => ['required', 'numeric', 'min:0'],
            "details.*.amounts.*.metadata" => ["required", 'array'],
            ...$this->storeApprovals(),
        ];
    }
}
