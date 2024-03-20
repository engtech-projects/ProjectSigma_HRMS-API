<?php

namespace App\Http\Controllers\Actions\SalaryGrade;

use App\Http\Controllers\Controller;
use App\Http\Resources\SalaryGradeLevelResource;
use App\Models\SalaryGradeLevel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalaryGradeLevelListController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $salaryGradeLevel = SalaryGradeLevel::select('salary_grade_levels.*', 'salary_grade_steps.*')->join('salary_grade_steps', 'salary_grade_steps.salary_grade_level_id', '=', 'salary_grade_levels.id')->get();
        if ($salaryGradeLevel->isEmpty()) {
            return new JsonResponse([
                "success" => false,
                "message" => "No data found.",
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            "success" => true,
            "message" => "Successfuly fetched.",
            "data" => SalaryGradeLevelResource::collection($salaryGradeLevel),
        ], JsonResponse::HTTP_OK);

        return $salaryGradeLevel;
    }
}
