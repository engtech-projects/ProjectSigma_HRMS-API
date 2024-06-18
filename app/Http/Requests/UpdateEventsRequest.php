<?php

namespace App\Http\Requests;

use App\Enums\EventTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateEventsRequest extends FormRequest
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
                "nullable",
                "string",
            ],
            'event_type' => [
                "nullable",
                "string",
                new Enum(EventTypes::class)
            ],
            'repetition_type' => [
                "nullable",
                'in:One Day,Long Event'
            ],
            'with_pay' => [
                "nullable",
                "boolean",
            ],
            'with_work' => [
                "nullable",
                "boolean",
            ],
            'start_date' => [
                "nullable",
                "date",
            ],
            'end_date' => [
                "nullable",
                "date",
            ],
            'description' => [
                "nullable",
                "string",
            ],
        ];
    }
}
