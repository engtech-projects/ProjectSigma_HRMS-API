<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Traits\HasApprovalValidation;


class UpdateallowanceRequest extends FormRequest
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
            'position_id' => [
                "nullable",
                "integer",
                "exists:positions,id",
            ],
            'amount' => [
                "nullable",
                "numeric",
                'max:999999',
                'decimal:0,2',
            ],
            ...$this->updateApprovals(),
        ];
    }
}
