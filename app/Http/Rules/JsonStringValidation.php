<?php

namespace App\Http\Rules;

use Illuminate\Contracts\Validation\Rule;

class JsonStringValidation implements Rule
{
    protected $keyMessages;

    public function __construct($keyMessages)
    {
        $this->keyMessages = $keyMessages;
    }

    public function passes($attribute, $value)
    {
        $jsonStringDecoded = json_decode($value, true);
        if ($jsonStringDecoded === null) {
            return false;
        }
        foreach ($jsonStringDecoded as $item) {
            foreach ($this->keyMessages as $key => $message) {
                if (!array_key_exists($key, $item)) {
                    $this->keyMessages = $message;
                    return false;
                }
            }
        }
        return true;
    }

    public function message()
    {
        return $this->keyMessages;
    }
}
