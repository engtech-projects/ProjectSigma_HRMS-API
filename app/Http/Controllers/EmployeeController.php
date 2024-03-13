<?php

namespace App\Http\Controllers;

use App\Enums\SearchTypes;
use App\Http\Requests\SearchEmployeeRequest;
use App\Models\Employee;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $main = Employee::simplePaginate(15);
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }

    public function search(SearchEmployeeRequest $request)
    {
        $validatedData = $request->validated();
        $searchKey = $validatedData["key"];
        $noAccounts = $validatedData["type"] == SearchTypes::NOACCOUNTS;
        return response()->json([$validatedData, $validatedData["type"], SearchTypes::NOACCOUNTS, $noAccounts]);
        $main = Employee::select("id", "first_name", "middle_name", "family_name")
            ->where(function ($q) use ($searchKey) {
                $q->orWhere('first_name', 'like', "%{$searchKey}%")
                    ->orWhere('family_name', 'like', "%{$searchKey}%");
                //     ->orWhere('middle_name', 'like', "%{$searchKey}%");
            })
            ->orWhere(DB::raw("CONCAT(family_name, ', ', first_name, ', ', middle_name)"), 'LIKE', $searchKey . "%")
            ->orWhere(DB::raw("CONCAT(first_name, ', ', middle_name, ', ', family_name)"), 'LIKE', $searchKey . "%")
            ->when($noAccounts, function (Builder $query, bool $noAccounts) {
                $query->doesntHave("account");
            })
            ->limit(25)
            ->orderBy('family_name')
            ->get()
            ->append(["fullname_last", "fullname_first"]);
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }

    public function get()
    {
        $main = Employee::with("company_employments", "employment_records")->get();
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        //
        $main = new Employee;
        $main->fill($request->validated());
        $data = json_decode('{}');

        if (!$main->save()) {
            $data->message = "Save failed.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Successfully save.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $main = Employee::with(
            "company_employments",
            "employment_records",
            "employee_address",
            "employee_affiliation",
            "employee_education",
            "employee_education_elementary:employee_id,elementary_name,elementary_education,elementary_period_attendance_to,elementary_period_attendance_from,elementary_year_graduated,elementary_degree_earned_of_school,elementary_honors_received",
            "employee_education_secondary:employee_id,secondary_name,secondary_education,secondary_period_attendance_to,secondary_period_attendance_from,secondary_year_graduated,secondary_degree_earned_of_school,secondary_honors_received",
            "employee_education_vocationalcourse:employee_id,vocationalcourse_name,vocationalcourse_education,vocationalcourse_period_attendance_to,vocationalcourse_period_attendance_from,vocationalcourse_year_graduated,college_degree_earned_of_school,college_honors_received",
            "employee_education_college:employee_id,college_name,college_education,college_period_attendance_to,college_period_attendance_from,college_year_graduated,vocationalcourse_degree_earned_of_school,vocationalcourse_honors_received",
            "employee_education_graduatestudies:employee_id,graduatestudies_name,graduatestudies_education,graduatestudies_period_attendance_to,graduatestudies_period_attendance_from,graduatestudies_year_graduated",
            "contact_person",
            "father",
            "spouse",
            "reference",
            "mother",
            "guardian",
            "child",
            "memo",
            "docs",
            "employee_eligibility",
            "masterstudies",
            "doctorstudies",
            "professionalstudies",
            "employee_seminartraining",
            "employee_internal",
            "employee_externalwork",
        )->get()->find($id);

        $data = json_decode('{}');
        if (!is_null($main)) {
            $main["age"] = $main->age;
            $data->message = "Successfully fetch.";
            $data->success = true;
            $data->data = $main;
            return response()->json($data);
        }
        $data->message = "No data found.";
        $data->success = false;
        return response()->json($data, 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request,  $id)
    {
        //
        $main = Employee::find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
            $main->fill($request->validated());
            if ($main->save()) {
                $data->message = "Successfully update.";
                $data->success = true;
                $data->data = $main;
                return response()->json($data);
            }
            $data->message = "Update failed.";
            $data->success = false;
            return response()->json($data, 400);
        }

        $data->message = "Failed update.";
        $data->success = false;
        return response()->json($data, 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $main = Employee::find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
            if ($main->delete()) {
                $data->message = "Successfully delete.";
                $data->success = true;
                $data->data = $main;
                return response()->json($data);
            }
            $data->message = "Failed delete.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Failed delete.";
        $data->success = false;
        return response()->json($data, 404);
    }
}
