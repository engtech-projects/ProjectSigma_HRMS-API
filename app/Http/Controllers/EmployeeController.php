<?php

namespace App\Http\Controllers;

use App\Enums\LeaveRequestType;
use App\Models\Employee;
use App\Enums\SearchTypes;
use App\Http\Requests\FilterDateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\SearchEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\ProjectResource;
use App\Models\AttendanceLog;
use App\Models\EmployeeLeaves;
use App\Models\Leave;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;

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
        $employeeList = Employee::with(['current_employment', 'employee_has_projects'])->get();
        $employeeCollection = collect($employeeList)->map(function ($employee) {
            $department = $employee->current_employment?->employee_department;
            $project = $employee->employee_has_projects->last();
            return [
                "id" => $employee->id,
                "first_name" => $employee->first_name,
                "middle_name" => $employee->middle_name,
                "family_name" => $employee->family_name,
                "fullname_last" => $employee->fullname_last,
                "fullname_first" => $employee->fullname_first,
                "name_suffix" => $employee->name_suffix,
                "nick_name" => $employee->nick_name,
                "gender" => $employee->gender,
                "department" => $department,
                "project" => $project ? [
                    "id" => $project->id,
                    "code" => $project->code,
                    "project_monitoring_id" => $project->project_monitoring_id,
                    "project_created_at" => $project->pivot->created_at,
                ] : null,
            ];
        });

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => $employeeCollection,
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
            "images",
        )->find($id);

        $data = json_decode('{}');
        if (!is_null($main)) {
            $main["age"] = $main->age;
            $main["profile_photo"] = $main->profile_photo;
            $main["digital_signature"] = $main->digital_signature;
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

    public function getAbsenceThisMonth(Schedule $req, AttendanceLog $log)
    {
        $getemployeeschedule = $req->scheduleEmployeeThisMonth($req);
        $absenceEmployeeData = [];
        foreach ($getemployeeschedule as $key => $value) {
            $from = $value->startRecur;
            $to = $value->endRecur;
            $maxDays = $to->diffInWeekdays($from);
            $EmployeeAbsence = $log->getAttendance($log, $value->employee_id, $value->startRecur, $value->endRecur);
            $absenceEmployeeData[$key]["employee_name"] = $value->employee->fullname_last;
            $absenceEmployeeData[$key]["absences"] = $maxDays - $EmployeeAbsence;
            if ($EmployeeAbsence >= $maxDays) {
                $absenceEmployeeData[$key]["absences"] = $maxDays;
            }
        }
        $dataval = collect($absenceEmployeeData)->unique();
        return new JsonResponse([
            'success' => 'true',
            'message' => 'Successfully fetched.',
            'data' => $dataval
        ]);
    }

    public function getLateThisMonth(Schedule $req, AttendanceLog $log)
    {
        $getemployeeschedule = $req->scheduleEmployeeThisMonth($req);
        $lateEmployeeData = [];
        foreach ($getemployeeschedule as $key => $value) {
            $EmployeeLate = $log->getLate($log, $value->employee_id, $value->startTime);
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

    public function getLeaveCredits($id)
    {
        $val = Employee::find($id);
        $leaves_type = Leave::get();
        if ($val) {
            $main = [];
            foreach ($leaves_type as $key) {
                $data = json_decode('{}');
                $count = EmployeeLeaves::where([
                    ["leave_id", $key->id],
                    ["request_status", "Approved"],
                ])->max('number_of_days');
                $leave = Leave::find($key->id);
                if ($leave) {
                    $data->leavename = $leave->leave_name;
                    $data->total_credits = $leave->amt_of_leave;
                    $data->used = $count;
                    $data->balance = $leave->amt_of_leave - $count;
                    array_push($main, $data);
                }
            }
            if ($main) {
                return new JsonResponse([
                    'success' => 'true',
                    'message' => 'Successfully fetch.',
                    'data' => $main,
                ]);
            }
        }
        return new JsonResponse([
            'success' => 'false',
            'message' => 'No data found.',
        ]);
    }
}
