<?php

namespace App\Enums;

enum AccessibilityAccounting: string
{
    case END_OF_CONTRACT = "End of Contract";
    case VIOLATION = "Violation";
    case SANCTIONS = "Sanctions";
    case FORCE_RESIGN = "Force Resign";
    case OTHERS = "Others";


    public static function toArray(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->name] = $case->value;
        }
        return $array;
    }

    public static function toArraySwapped(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->value] = $case->name;
        }
        return $array;
    }
}
