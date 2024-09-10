<?php

namespace App\Http\Requests;

use App\Enums\NotificationActions;
use App\Enums\NotificationModules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ApiNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "message" => [
                "required",
                'string',
                'max:200',
            ],
            "module" => [
                "required",
                'string',
                'max:200',
                new Enum(NotificationModules::class)
            ],
            "request_type" => [
                "required",
                'string',
                'max:200',
            ],
            "request_id" => [
                "required",
            ],
            "action" => [
                "required",
                'string',
                'max:200',
                new Enum(NotificationActions::class)
            ],
        ];
    }
}
