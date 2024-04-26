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
        $employees = Employee::select(['first_name', 'family_name', 'middle_name', 'date_of_birth'])->whereMonth('date_of_birth', $month)->get();

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => $employees,
        ]);
    }
}
