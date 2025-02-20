<?php

namespace App\Http\Traits;

use App\Enums\AccessibilityHrms;
use App\Enums\UserTypes;
use Illuminate\Support\Facades\Auth;

trait CheckAccessibility
{
    public function checkUserAccess($allowedAccessibilities)
    {
        $allowedAccessibilities[] = AccessibilityHrms::SUPERADMIN->value; // ADDED FOR AUTO ALLOW SUPER ADMIN ACCESS
        $userAccessibilities = Auth::user()->accessibility_names;
        if (Auth::user()->type == UserTypes::ADMINISTRATOR->value) {
            return true;
        }
        return collect($userAccessibilities)->contains(function ($item) use ($allowedAccessibilities) {
            return in_array($item, $allowedAccessibilities);
        });
    }
}
