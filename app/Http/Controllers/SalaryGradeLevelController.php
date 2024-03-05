<?php

namespace App\Http\Controllers;

use App\Http\Resources\SalaryGradeLevelResource;
use App\Models\SalaryGradeLevel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
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
        DB::transaction(function () use ($attributes) {
            $salaryGradeLevel = SalaryGradeLevel::create($attributes);
            $salaryGradeLevel->salary_grade_step()->createMany($attributes['salary_grade_step']);
        });

        return new JsonResponse(["message" => "Salary grade level created."], JsonResponse::HTTP_CREATED);
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
        DB::transaction(function () use ($attributes, $salaryGradeLevel) {
            $salaryGradeLevel->fill($attributes);
            $salaryGradeLevel->salary_grade_step()->createMany($attributes['salary_grade_step']);
            $salaryGradeLevel->update();
        });

        return new JsonResponse(["message" => "Salary grade level updated."], JsonResponse::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalaryGradeLevel $salaryGradeLevel)
    {
        //
    }
}
