<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Enums\SearchTypes;
use App\Http\Requests\FilterDateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\SearchEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeDetailedEnumResource;
use App\Http\Resources\EmployeeInfos\CompleteDetailsResource;
use App\Models\AttendanceLog;
use App\Models\EmployeeLeaves;
use App\Models\Leave;
use App\Models\Schedule;

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
        $withAccounts = $validatedData["type"] == SearchTypes::WITHACCOUNTS->value;
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
            ->when($withAccounts, function (Builder $builder) {
                $builder->whereHas("account");
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
        $employeeList = Employee::with(['current_employment.position', 'current_employment.projects', "company_employments"])->orderBy('family_name')->get();

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => EmployeeDetailedEnumResource::collection($employeeList),
        ]);
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
            "permanent_address",
            "present_address",
            "current_employment.employee_salarygrade.salary_grade_level",
            "current_employment.position",
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
            "employee_internal.position",
            "employee_internal.projects",
            "employee_externalwork",
            "images",
            'face_patterns',
        )->find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
            $main["age"] = $main->age;
            $main["profile_photo"] = $main->profile_photo?->append("base64");
            $main["digital_signature"] = $main->digital_signature?->append("base64");
            $data->message = "Successfully fetch.";
            $data->success = true;
            $data->data = new CompleteDetailsResource($main);
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
            if ($main->update($request->validated())) {
                $data->message = "Successfully update.";
                $data->success = true;
                $data->data = $main->refresh();
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

    public function getFilterLate(Schedule $req, AttendanceLog $log, FilterDateRequest $request)
    {
        $main = $request->validated();
        if (!array_key_exists("start_date", $main)) {
            $main["start_date"] = "";
        }
        if (!array_key_exists("end_date", $main)) {
            $main["end_date"] = "";
        }

        $getemployeeschedule = $req->scheduleEmployeeDateFilter($req, $main["start_date"], $main["end_date"]);
        $lateEmployeeData = [];

        foreach ($getemployeeschedule as $key => $value) {
            $EmployeeLate = $log->getFilterLate($log, $value->employee_id, $value->startTime, $value->startRecur, $main["end_date"]);
            if ($EmployeeLate > 0) {
                $lateEmployeeData[$key]["employee_name"] = $value->employee->fullname_last;
                $lateEmployeeData[$key]["lates"] = $EmployeeLate;
            }
        }
        $dataval = collect($lateEmployeeData)->unique();
        return new JsonResponse([
            'success' => 'true',
            'message' => 'Successfully fetched.',
            'data' => $dataval
        ]);
    }

    public function getEmployeeLeaveCredits($val)
    {
        $leaves_type = Leave::get();
        if ($val) {
            $leavedata = [];
            $getData = json_decode('{}');
            foreach ($leaves_type as $key) {
                $data = json_decode('{}');
                if (gettype($key->employment_status) == "string") {
                    $type = json_decode($key->employment_status);
                    if ($val->current_employment) {
                        if (in_array($val->current_employment->employment_status, $type)) {
                            $count = EmployeeLeaves::where([
                                ["leave_id", $key->id],
                                ["request_status", "Approved"],
                            ])->max('number_of_days');
                            $leave = Leave::find($key->id);
                            if ($leave) {
                                $data->leavename = $leave->leave_name;
                                $data->total_credits = $leave->amt_of_leave;
                                $data->used = $count ?? 0;
                                $data->balance = $leave->amt_of_leave - $count;
                                array_push($leavedata, $data);
                            }
                        } else {
                            $leave = Leave::find($key->id);
                            $data->leavename = $leave->leave_name;
                            $data->total_credits = $leave->amt_of_leave;
                            $data->used = 0;
                            $data->balance = $leave->amt_of_leave;
                            array_push($leavedata, $data);
                        }
                    } else {
                        $leave = Leave::find($key->id);
                        if ($leave) {
                            $data->leavename = $leave->leave_name;
                            $data->total_credits = $leave->amt_of_leave;
                            $data->used = 0;
                            $data->balance = $leave->amt_of_leave;
                            array_push($leavedata, $data);
                        }
                    }
                }
            }
            $getData->employee = $val;
            $getData->employee->leaveCredits = $leavedata;
            if ($val) {
                return $getData;
            }
        }
        return $getData;
    }

    public function getLeaveCredits(Employee $employee)
    {
        return new JsonResponse([
            'success' => 'true',
            'message' => 'Successfully fetch.',
            'data' => $employee->leaveCredits,
        ]);
    }
}
