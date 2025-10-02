<?php

namespace App\Http\Controllers;

use App\Exceptions\TransactionFailedException;
use Exception;
use App\Models\SalaryGradeStep;
use App\Models\SalaryGradeLevel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\SalaryGradeLevelResource;
use App\Http\Requests\StoreSalaryGradeLevelRequest;
use App\Http\Requests\UpdateSalaryGradeLevelRequest;

class SalaryGradeLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $salaryGradeLevel = SalaryGradeLevel::with(['salary_grade_step' => function ($query) {
            $query->orderBy('step_name');
        }])
        ->orderByRaw('LENGTH(salary_grade_level)')
        ->orderBy('salary_grade_level')
        ->get();

        if ($salaryGradeLevel->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            "success" => true,
            "message" => "Succcessfully fetched.",
            "data" => SalaryGradeLevelResource::collection($salaryGradeLevel)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSalaryGradeLevelRequest $request)
    {
        $attributes = $request->validated();
        try {
            DB::transaction(function () use ($attributes) {
                $salaryGradeLevel = SalaryGradeLevel::create($attributes);
                $salaryGradeLevel->salary_grade_step()->createMany($attributes['salary_grade_step']);
            });
        } catch (Exception $e) {
            throw new TransactionFailedException("Create transaction failed.", 400, $e);
        }

        return new JsonResponse([
            "success" => true,
            "message" => "Successfully created."
        ], JsonResponse::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(SalaryGradeLevel $resource)
    {
        return new SalaryGradeLevelResource($resource->load('salary_grade_step'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSalaryGradeLevelRequest $request, SalaryGradeLevel $resource)
    {
        $attributes = $request->validated();
        try {
            DB::transaction(function () use ($attributes, $resource) {
                $resource = $resource->fill($attributes);
                $resource->update();

                $salaryGradeStep = $resource->salary_grade_step;
                foreach ($attributes["salary_grade_step"] as $attribute) {
                    $salaryGradeStep = SalaryGradeStep::find($attribute["id"]);
                    if ($salaryGradeStep) {
                        $salaryGradeStep->update([
                            "step_name" => $attribute["step_name"],
                            "monthly_salary_amount" => $attribute["monthly_salary_amount"],
                        ]);
                    }
                }
            });
        } catch (Exception $e) {
            throw new TransactionFailedException("Update transaction failed.", 400, $e);
        }

        return new JsonResponse([
            "success" => true,
            "message" => "Salary grade level updated."
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalaryGradeLevel $resource)
    {
        try {
            $resource->delete();
        } catch (Exception $e) {
            throw new TransactionFailedException("Delete transaction failed.", 400, $e);
        }

        return new JsonResponse([
            "success" => true,
            "message" => "Salary grade level deleted."
        ], JsonResponse::HTTP_OK);
    }
}
