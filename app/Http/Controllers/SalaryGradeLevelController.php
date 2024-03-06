<?php

namespace App\Http\Controllers;

use App\Exceptions\TransactionFailedException;
use Exception;
use App\Models\SalaryGradeStep;
use App\Models\SalaryGradeLevel;
use Illuminate\Database\QueryException;
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
        $salaryGradeLevel = SalaryGradeLevel::with(['salary_grade_step'])->get();

        return SalaryGradeLevelResource::collection($salaryGradeLevel);
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


        return new JsonResponse(["message" => "Salary grade level and steps created."], JsonResponse::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(SalaryGradeLevel $salaryGradeLevel)
    {
        return new SalaryGradeLevelResource($salaryGradeLevel->load('salary_grade_step'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSalaryGradeLevelRequest $request, SalaryGradeLevel $salaryGradeLevel)
    {
        $attributes = $request->validated();
        try {
            DB::transaction(function () use ($attributes, $salaryGradeLevel) {
                $salaryGradeLevel = $salaryGradeLevel->fill($attributes);
                $salaryGradeLevel->update();

                $salaryGradeStep = $salaryGradeLevel->salary_grade_step;
                foreach ($salaryGradeStep as $value) {
                    foreach ($attributes["salary_grade_step"] as $attribute) {
                        if ($attribute['id'] == $value->id) {
                            $salaryGradeStep = SalaryGradeStep::find($value->id);
                            $salaryGradeStep->update([
                                "step_name" => $attribute["step_name"]
                            ]);
                        }
                    }
                }
            });
        } catch (Exception $e) {
            throw new TransactionFailedException("Update transaction failed.", 400, $e);
        }

        return new JsonResponse(["message" => "Salary grade level updated."], JsonResponse::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalaryGradeLevel $salaryGradeLevel)
    {
        try {
            $salaryGradeLevel->delete();
        } catch (Exception $e) {
            throw new TransactionFailedException("Delete transaction failed.", 400, $e);
        }

        return new JsonResponse(["message" => "Salary grade level deleted."], JsonResponse::HTTP_OK);
    }
}
