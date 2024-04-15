<?php

namespace App\Http\Controllers\Actions\Employee;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class CountEmployeeGenderController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $total = Employee::get(['gender']);

        $total = collect($total)->map(function ($employee) {
            return [
                'gender' => strtolower($employee['gender'])
            ];
        })->groupBy('gender')->map(function ($employee) {
            return $employee->count();
        });

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => $total
        ]);
    }
}
