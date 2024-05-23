<?php

namespace App\Http\Controllers;

use App\Enums\UserTypes;
use App\Models\Accessibilities;
use App\Http\Requests\StoreAccessibilitiesRequest;
use App\Http\Requests\UpdateAccessibilitiesRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AccessibilitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $showSetupSalary = $request->user()->type == UserTypes::ADMINISTRATOR->value || in_array($request->user()->id, config('app.salary_grade_setter'));
        $access = Accessibilities::when(!$showSetupSalary , function (Builder $builder) {
            $builder->where("accessibilities_name", "!=", Accessibilities::HRMS_SETUP_SALARY_GRADE);
        })->orderBy("accessibilities_name")->get();
        return response()->json([
            "data" => $access,
            "message" => "Success Get Accessibilities List",
            "success" => true,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAccessibilitiesRequest $request)
    {
        $access = new Accessibilities();
        $access->fill($request->validated());
        if (!$access->save()) {
            return response()->json(["msg" => "error"], 400);
        }
        return response()->json($access);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $accessibilities = Accessibilities::find($id);
        return response()->json($accessibilities);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccessibilitiesRequest $request, $id)
    {
        $accessibilities = Accessibilities::find($id);
        $accessibilities->fill($request->validated());
        if ($accessibilities->save()) {
            return response()->json($accessibilities);
        }
        return response()->json(["msg" => "error"], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $accessibilities = Accessibilities::find($id);
        if ($accessibilities->delete()) {
            return response()->json($accessibilities);
        }
        return response()->json(["msg" => "error"], 400);
    }
}
