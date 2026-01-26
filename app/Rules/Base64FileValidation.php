<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Base64FileValidation implements Rule
{
    public function passes($attribute, $value)
    {
        $mime =  mime_content_type($value);
        if (empty($value)) {
            return false;
        }
        if ($mime === 'image/png') {
            return true;
        }
    }
    public function message()
    {
        return "Digital signature must be a .png file type.";
    }
}
