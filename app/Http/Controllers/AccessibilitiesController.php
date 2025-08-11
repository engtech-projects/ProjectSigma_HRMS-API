<?php

namespace App\Http\Controllers;

use App\Enums\AccessibilityHrms;
use App\Enums\SetupSettingsEnums;
use App\Enums\UserTypes;
use App\Models\Accessibilities;
use App\Http\Requests\StoreAccessibilitiesRequest;
use App\Http\Requests\UpdateAccessibilitiesRequest;
use App\Http\Traits\CheckAccessibility;
use App\Models\Settings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AccessibilitiesController extends Controller
{
    use CheckAccessibility;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $showSuperAdmin = $this->checkUserAccess([]);
        $salaryGradeSetterSettings = Settings::settingName(SetupSettingsEnums::USER_SALARY_GRADE_SETTER)->first()->value;
        $salaryGradeSetterIds = array_map('intval', explode(',',  $salaryGradeSetterSettings));
        $edit201SetterSettings = Settings::settingName(SetupSettingsEnums::USER_201_EDITOR)->first()->value;
        $edit201SetterIds = array_map('intval', explode(',', $edit201SetterSettings));
        $showSetupSalary = $request->user()->type == UserTypes::ADMINISTRATOR->value || in_array($request->user()->id, $salaryGradeSetterIds);
        $showEdit201 = $request->user()->type == UserTypes::ADMINISTRATOR->value || in_array($request->user()->id, $edit201SetterIds);
        $access = Accessibilities::when(!$showSetupSalary, function (Builder $builder) {
            $builder->where("accessibilities_name", "!=", AccessibilityHrms::HRMS_SETUP_SALARY_GRADE->value);
        })
        ->when(!$showEdit201, function (Builder $builder) {
            $builder->where("accessibilities_name", "!=", AccessibilityHrms::HRMS_EMPLOYEE_201_EDIT->value);
        })
        ->when(!$showSuperAdmin, function (Builder $builder) {
            $builder->where("accessibilities_name", "!=", AccessibilityHrms::SUPERADMIN->value);
        })
        ->orderBy("accessibilities_name")->get();
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
