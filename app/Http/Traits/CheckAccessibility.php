<?php

namespace App\Http\Traits;

use App\Enums\UserTypes;
use Illuminate\Support\Facades\Auth;

trait CheckAccessibility
{
    public function checkUserAccess($allowedAccessibilities)
    {
        $userAccessibilities = Auth::user()->accessibility_names;
        if (Auth::user()->type == UserTypes::ADMINISTRATOR->value) {
            return true;
        }
        $userAllowed = false;
        $allowedAccessibilities->each(function($element) use($userAccessibilities, $userAllowed) {
            $userAccessibilities->each(function($useraccess) use($element, $userAllowed) {
                if ($useraccess->startsWith($element)) {
                    $userAllowed = true;
                }
            });
        });
        return $userAllowed;
    }
}
