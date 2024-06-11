<?php

namespace App\Http\Controllers\Actions\Employee;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;

class MonthlyBirthdaysController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $month = Carbon::now()->format('m');
        $employees = Employee::with("profile_photo")
        ->whereMonth('date_of_birth', $month)
        ->orderByRaw('DAY(date_of_birth)')
        ->get();

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => $employees,
        ]);
    }
}
