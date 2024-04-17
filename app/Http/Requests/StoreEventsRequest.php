<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventsRequest extends FormRequest
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
            'title' => [
                "required",
                "string",
            ],
            'event_type' => [
                "required",
                'in:Holiday,Company Event'
            ],
            'repetition_type' => [
                "required",
                'in:One Day,Long Event'
            ],
            'with_pay' => [
                "required",
                "boolean",
            ],
            'with_work' => [
                "required",
                "boolean",
            ],
            'start_date' => [
                "required",
                "date",
            ],
            'end_date' => [
                "required",
                "date",
            ],
            'description' => [
                "required",
                "string",
            ],
        ];
    }
}
