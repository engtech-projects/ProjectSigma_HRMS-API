<?php

namespace App;

use Illuminate\Support\Carbon;

class Helpers
{
    public static function dateRange($dateRange)
    {
        $dates = [];

        $start = Carbon::parse($dateRange["period_start"]);
        $end = Carbon::parse($dateRange["period_end"]);
        for ($date = $start; $date->lte($end); $date->addDay()) {
            $dates[] = [
                "date" => $date->format('Y-m-d')
            ];
        }

        return $dates;
    }
}
