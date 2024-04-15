<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Enums\SearchTypes;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\SearchEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\ProjectResource;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $main = Employee::paginate(15);
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
        $noAccounts = $validatedData["type"] == SearchTypes::NOACCOUNTS->value;
        $main = Employee::select("id", "first_name", "middle_name", "family_name")
            ->where(function ($q) use ($searchKey) {
                $q->orWhere('first_name', 'like', "%{$searchKey}%")
                    ->orWhere('family_name', 'like', "%{$searchKey}%")
                    //     ->orWhere('middle_name', 'like', "%{$searchKey}%");
                    ->orWhere(
                        DB::raw("CONCAT(family_name, ', ', first_name, ' ', middle_name)"),
                        'LIKE',
                        $searchKey . "%"
                    )
                    ->orWhere(
                        DB::raw("CONCAT(first_name, ' ', middle_name, ' ', family_name)"),
                        'LIKE',
                        $searchKey . "%"
                    );
            })
            ->when($noAccounts, function (Builder $builder) {
                $builder->whereDoesntHave("account");
            })
            ->with("account")
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
        $employeeList = Employee::whereHas('employee_internal', function ($query) {
            $query->statusCurrent();
        })->with(['employee_internal' => function ($query) {
            $query->withOut(['employee_salarygrade']);
        }, 'employee_has_projects'])->get();

        $employeeCollection = collect($employeeList)->map(function ($employee) {
            $department = $employee->employee_internal->first()->employee_department;
            $project = $employee->employee_has_projects->last();
            return [
                "id" => $employee->id,
                "first_name" => $employee->first_name,
                "middle_name" => $employee->middle_name,
                "family_name" => $employee->family_name,
                "name_suffix" => $employee->name_suffix,
                "nick_name" => $employee->nick_name,
                "gender" => $employee->gender,
                "department" => $department,
                "project" => [
                    "id" => $project->id,
                    "code" => $project->code,
                    "project_monitoring_id" => $project->code,
                    "project_created_at" => $project->pivot->created_at,
                ]
            ];
        });

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => $employeeCollection,
        ]);

        /* $main = Employee::with("company_employments", "employment_records")->get();
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data); */
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $main = new Employee();
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
        $main = Employee::with(
            "company_employments",
            "employment_records",
            "employee_address",
            "current_employment.employee_salarygrade.salary_grade_level",
            "employee_affiliation",
            "employee_education",
            "employee_education_elementary",
            "employee_education_secondary",
            "employee_education_vocationalcourse",
            "employee_education_college",
            "employee_education_graduatestudies",
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
            "employee_internal.employee_salarygrade.salary_grade_level",
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
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, $id)
    {
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
