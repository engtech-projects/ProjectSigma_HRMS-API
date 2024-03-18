<?php

namespace App\Http\Controllers;

use App\Enums\EmployeeAddressType;
use App\Enums\EmployeeCompanyEmploymentsStatus;
use App\Enums\EmployeeInternalWorkExperiencesStatus;
use App\Enums\EmployeeRelatedPersonType;
use App\Http\Requests\EmployeeInternalWorkExperience;
use App\Http\Requests\StoreDisapprove;
use App\Models\EmployeePersonnelActionNoticeRequest;
use App\Http\Requests\StoreEmployeePersonnelActionNoticeRequestRequest;
use App\Http\Requests\UpdateEmployeePersonnelActionNoticeRequestRequest;
use App\Models\Employee;
use App\Models\InternalWorkExperience;
use App\Models\JobApplicants;
use App\Models\ManpowerRequest;
use App\Models\SalaryGradeStep;
use App\Models\Termination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;

class EmployeePersonnelActionNoticeRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $main = EmployeePersonnelActionNoticeRequest::with('employee', 'jobapplicantonly', 'department')->paginate(15);
        $data = json_decode('{}');
        foreach ($main as $key => $value) {
            $pendingData = [];
            foreach (json_decode($value->approvals) as $approval_key) {
                $getName = Employee::where("id", $approval_key->user_id)->first()->append("fullnameLast")->fullnameLast;
                $approval_key->name = $getName;
                array_push($pendingData, $approval_key);
            }
            $main[$key]->approvals = json_encode($pendingData);
        }

        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeePersonnelActionNoticeRequestRequest $request)
    {
        $id = Auth::user()->id;
        $main = new EmployeePersonnelActionNoticeRequest();
        $main->created_by = $id;
        $validData = $request->validated();
        $main->fill($validData);
        $data = json_decode('{}');
        $main->approvals = json_encode($validData["approvals"]);
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

    // can view all pan request made by logged in user
    public function getpanrequest()
    {
        $id = Auth::user()->id;
        $main = EmployeePersonnelActionNoticeRequest::with('department')->where("created_by", "=", $id)->get();
        $data = json_decode('{}');

        foreach ($main as $key => $value) {
            $pendingData = [];
            foreach (json_decode($value->approvals) as $approval_key) {
                $getName = Employee::where("id", $approval_key->user_id)->first()->append("fullnameLast")->fullnameLast;
                $approval_key->name = $getName;
                array_push($pendingData, $approval_key);
            }
            $main[$key]->approvals = json_encode($pendingData);
        }

        if (!is_null($main)) {
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
     * Show can view all pan request to be approved by logged in user (same login in manpower request)
     */
    public function getApprovals()
    {
        $id = Auth::user()->id;
        $main = EmployeePersonnelActionNoticeRequest::with('department')->approval()
        ->whereJsonContains('approvals', ["user_id" => $id, "status" => "Pending"])->get();
        $newdata = json_decode('{}');
        foreach ($main as $key => $value) {
            $pendingData = collect(json_decode($value->approvals))->where("user_id", $id)->where("status", "Pending");
            $get_approval = collect(json_decode($value->approvals))->where("status", "Pending")->first();
            $next_approval = $pendingData[0]->user_id;
            if ($get_approval) {
                $next_approval = $get_approval->user_id;
            }
            if ($next_approval == $id) {
                $getName = Employee::where("id", $pendingData[0]->user_id)->first()->append("fullnameLast")->fullnameLast;
                $pendingData[0]->name = $getName;
                $main[$key]->approvals = $pendingData;
            }
        }
        $newdata->message = "Successfully fetch.";
        $newdata->success = true;
        $newdata->data = $main;
        return response()->json($newdata);
    }

    // logged in can approve pan request(if he is the current approval)
    public function approveApprovals($request)
    {

        $id = Auth::user()->id;
        $main = EmployeePersonnelActionNoticeRequest::where("id", $request)->with("jobapplicant", "salarygrade")->first();
        $newdata = json_decode('{}');

        if (!$main) {
            return $this->failedMessage($newdata, "No Request found.");
        }

        $panreq = EmployeePersonnelActionNoticeRequest::select('approvals')->where("id", "=", $request)->approval()->first();
        $get_approval = collect(json_decode($main->approvals))->where("status", "Pending")->first();
        $next_approval = 0;
        if ($get_approval) {
            $next_approval = $get_approval->user_id;
        }
        $count_approves = collect(json_decode($main->approvals))->where("status", "Approved")->count();
        $approve = 0;
        if (!$panreq) {
            $newdata->success = false;
            $newdata->message = "No pending data found.";
            return response()->json($newdata);
        }

        $count = count(json_decode($panreq->approvals));

        if ($next_approval == $id) {
            $a = [];
            foreach (json_decode($panreq->approvals) as $key) {
                if ($key->user_id == $id && $key->status == "Pending" && $approve == 0) {
                    $key->status = "Approved";
                    $key->date_approved = Carbon::now()->format('Y-m-d');
                    $approve = 1;
                    $count_approves += 1;
                } elseif ($key->status == "Approved") {
                    $count_approves += 1;
                }
                array_push($a, $key);
            }

            if ($count_approves >= $count) {
                // Approved All on Panreq
                if ($main->type == "New Hire") {
                    $saveData = $this->hireApproved($main->pan_job_applicant_id, $main);
                    if ($saveData) {
                        $main->jobapplicant->status = "Hired";
                        JobApplicants::where("id", $main->pan_job_applicant_id)->update(["status" => "Hired"]);
                        ManpowerRequest::where("id", $main->jobapplicant->manpower->id)->update(["request_status" => "Approved"]);
                        $main->request_status = "Filled";
                    } else {
                        return $this->failedMessage($newdata, "Failed approved.");
                    }
                }
                // Approved Transfer Data
                if ($main->type == "Transfer") {
                    $this_internal_id = InternalWorkExperience::select("id")->where(
                        [
                            ["id", "=",$main->employee_id],
                            ["date_to", "=", null],
                            ["status","=", EmployeeInternalWorkExperiencesStatus::CURRENT]
                        ]
                    )->first();
                    if ($this_internal_id) {
                        $saveData = $this->transferData($this_internal_id->id, $main);
                    } else {
                        return $this->failedMessage($newdata, "Failed transfer.");
                    }
                }

                // Approved Promotion Data
                if ($main->type == "Promotion") {
                    $this_internal_id = InternalWorkExperience::select("id")->where(
                        [
                            ["id", "=",$main->employee_id],
                            ["date_to", "=", null],
                            ["status","=", EmployeeInternalWorkExperiencesStatus::CURRENT]
                        ]
                    )->first();
                    if ($this_internal_id) {
                        $saveData = $this->promotionData($this_internal_id->id, $main);
                    } else {
                        return $this->failedMessage($newdata, "Failed promotion.");
                    }
                }

                // Approved Termination
                if ($main->type == "Termination") {
                    $this_internal_id = InternalWorkExperience::select("id")->where(
                        [
                            ["id", "=", $main->employee_id],
                            ["date_to", "=", null],
                            ["status","=", EmployeeInternalWorkExperiencesStatus::CURRENT]
                        ]
                    )->first();
                    if ($this_internal_id) {
                        $saveData = $this->termination($this_internal_id->id, $main);
                    } else {
                        return $this->failedMessage($newdata, "Failed termination.");
                    }
                }
            }

            $main->approvals = json_encode($a);
            $main->save();

            if ($main) {
                $newdata->success = true;
                $newdata->message = "Successfully approved.";
                $newdata->data = $main;
                return response()->json($newdata);
            }
        }

        return $this->failedMessage($newdata, "Failed approved.");
    }

    // logged in can approve pan request(if he is the current approval)
    public function disapproveApprovals(StoreDisapprove $request)
    {
        $id = Auth::user()->id;
        $main = EmployeePersonnelActionNoticeRequest::where("id", $request->id)->with("jobapplicant", "salarygrade")->first();
        $newdata = json_decode('{}');

        if (!$main) {
            return $this->failedMessage($newdata, "No data found.");
        }

        $panreq = EmployeePersonnelActionNoticeRequest::select('approvals')->where("id", "=", $request->id)->approval()->first();
        $get_approval = collect(json_decode($main->approvals))->where("status", "Pending")->first();
        $next_approval = 0;
        if ($get_approval) {
            $next_approval = $get_approval->user_id;
        }

        if (!$panreq) {
            $newdata->success = false;
            $newdata->message = "No data found.";
            return response()->json($newdata);
        }

        $disApprove = 0;
        if ($next_approval == $id) {
            $a = [];
            foreach (json_decode($panreq->approvals) as $key) {
                if ($key->user_id == $id && $key->status == "Pending" && $disApprove == 0) {
                    $key->status = "Disapproved";
                    $key->remarks = $request->remarks;
                    $disApprove = 1;
                }
                array_push($a, $key);
            }
            $main->approvals = json_encode($a);
            $main->save();

            if ($main) {
                $newdata->success = true;
                $newdata->message = "Successfully disapproved.";
                $newdata->data = $main;
                return response()->json($newdata);
            }
        }
        return $this->failedMessage($newdata, "Failed disapproved.");
    }

    public function failedMessage($newdata, $message)
    {
        $newdata->success = false;
        $newdata->message = $message;
        // $newdata->message = "Failed approved.";
        return response()->json($newdata);
    }

    public function hireApproved($id, $request)
    {
        $main = JobApplicants::where('id', '=', $id)->first();
        $employee = new Employee();
        $data = [];
        $internalWork = [];
        $preAddress = [];
        $perAddress = [];
        $externalWorkExperience = [];
        $relatedPersonSpouse = [];
        $relatedPersonFather = [];
        $relatedPersonMother = [];
        $relatedInCaseEmergency = [];
        $relatedChildren = [];
        $employeecompany = [];

        if (!$main) {
            return false;
        }

        // Employee
        $data["employee"] = $main->lastname;
        $data["family_name"] = $main->lastname;
        $data["first_name"] = $main->firstname;
        $data["middle_name"] = $main->middlename;
        $data["name_suffix"] = $main->name_suffix;
        $data["gender"] = $main->gender;
        $data["date_of_birth"] = $main->date_of_birth;
        $data["place_of_birth"] = $main->place_of_birth;
        $data["date_of_mdataiage"] = $main->date_of_mdataiage;
        $data["citizenship"] = $main->citizenship;
        $data["blood_type"] = $main->blood_type;
        $data["civil_status"] = $main->civil_status;
        $data["mobile_number"] = $main->contact_info;
        $data["email"] = $main->email;
        $data["religion"] = $main->religion;
        $data["weight"] = $main->weight;
        $data["height"] = $main->height;

        // Internal Work Experience
        $internalWork['position_title'] = $request->designation_position;
        $internalWork['employment_status'] = $request->employement_status;
        $internalWork['department'] = $request->section_department_id;
        $internalWork['immediate_supervisor'] = $request->immediate_supervisor ?? "N/A";
        $internalWork['actual_salary'] = $request->salarygrade->monthly_salary_amount;
        $internalWork['work_location'] = $request->work_location;
        $internalWork['hire_source'] = $request->hire_source;
        $internalWork['status'] = EmployeeInternalWorkExperiencesStatus::CURRENT;
        $internalWork['date_from'] = $request->date_from;
        $internalWork['date_to'] = null;
        $internalWork['salary_grades'] = $request->salary_grades;

        // Employee Address
        $preAddress["street"] = $main->pre_address_street;
        $preAddress["brgy"] = $main->pre_address_brgy;
        $preAddress["city"] = $main->pre_address_city;
        $preAddress["zip"] = $main->pre_address_zip;
        $preAddress["province"] = $main->pre_address_province;
        $preAddress["type"] = EmployeeAddressType::PRESENT;
        $perAddress["street"] = $main->per_address_street;
        $perAddress["brgy"] = $main->per_address_brgy;
        $perAddress["city"] = $main->per_address_city;
        $perAddress["zip"] = $main->per_address_zip;
        $perAddress["province"] = $main->per_address_province;
        $perAddress["type"] = EmployeeAddressType::PERMANENT;

        // Employee Company
        $employeecompany["employeedisplay_id"] = null;
        $employeecompany["date_hired"] = $request->date_of_effictivity;
        $employeecompany["phic_number"] = $main->philhealth;
        $employeecompany["sss_number"] = $main->sss;
        $employeecompany["tin_number"] = $main->tin;
        $employeecompany["pagibig_number"] = $main->pagibig;
        $employeecompany["status"] = EmployeeCompanyEmploymentsStatus::ACTIVE;

        // Employee Spouse
        $relatedPersonSpouse["name"] = $main->name_of_spouse;
        $relatedPersonSpouse["date_of_birth"] = $main->date_of_birth_spouse ?? null;
        $relatedPersonSpouse["contact_no"] = $main->telephone_spouse ?? null;
        $relatedPersonSpouse["occupation"] = $main->occupation_spouse ?? null;
        $relatedPersonSpouse["type"] = EmployeeRelatedPersonType::SPOUSE;

        // Employee Father
        $relatedPersonFather["name"] = $main->father_name;
        $relatedPersonFather["type"] = EmployeeRelatedPersonType::FATHER;

        // Employee Mother
        $relatedPersonMother["name"] = $main->mother_name;
        $relatedPersonMother["type"] = EmployeeRelatedPersonType::MOTHER;

        // Employee In Case of Emergency
        $relatedInCaseEmergency["name"] = $main->icoe_name ?? null;
        $relatedInCaseEmergency["street"] = $main->icoe_street ?? null;
        $relatedInCaseEmergency["brgy"] = $main->icoe_brgy ?? null;
        $relatedInCaseEmergency["city"] = $main->icoe_city ?? null;
        $relatedInCaseEmergency["zip"] = $main->icoe_zip ?? null;
        $relatedInCaseEmergency["province"] = $main->icoe_province ?? null;
        $relatedInCaseEmergency["relationship"] = $main->icoe_relationship ?? null;
        $relatedInCaseEmergency["contact_no"] = $main->telephone_icoe ?? null;
        $relatedInCaseEmergency["type"] = EmployeeRelatedPersonType::CONTACT_PERSON;

        $employee->fill($data)->save();
        $employee->employee_internal()->create($internalWork);
        $employee->employee_address()->create($preAddress);
        $employee->employee_address()->create($perAddress);
        $employee->company_employments()->create($employeecompany);

        if (property_exists("name_of_spouse", $main)) {
            $employee->employee_related_person()->create($relatedPersonSpouse);
        }
        if (property_exists("father_name", $main)) {
            $employee->employee_related_person()->create($relatedPersonFather);
        }
        if (property_exists("mother_name", $main)) {
            $employee->employee_related_person()->create($relatedPersonMother);
        }
        if (property_exists("icoe_name", $main)) {
            $employee->employee_related_person()->create($relatedInCaseEmergency);
        }

        // Employee Children
        if (property_exists("children", $main)) {
            foreach (json_decode($main->children) as $key) {
                $relatedChildren["name"] = $key->name;
                $relatedChildren["date_of_birth"] = $key->birthdate ?? null;
                $relatedChildren["type"] = EmployeeRelatedPersonType::CHILD;
                $employee->employee_related_person()->create($relatedChildren);
            }
        }

        // Employee Work Experience
        if (property_exists("workexperience", $main)) {
            foreach (json_decode($main->workexperience) as $key) {
                $externalWorkExperience["date_from"] = $key->inclusive_dates_from ?? null;
                $externalWorkExperience["date_to"] = $key->inclusive_dates_to ?? null;
                $externalWorkExperience["position_title"] = $key->position_title ?? null;
                $externalWorkExperience["company_name"] = $key->dpt_agency_office_company ?? null;
                $externalWorkExperience["salary"] = $key->monthly_salary ?? null;
                $externalWorkExperience["status_of_appointment"] = $key->status_of_appointment ?? null;
                $employee->employee_externalwork()->create($externalWorkExperience);
            }
        }
        // Employee Education
        return true;
    }

    public function transferData($id, $request)
    {
        $data = InternalWorkExperience::where("id", "=", $id)->first();
        $data->status = EmployeeInternalWorkExperiencesStatus::PREVIOUS;
        $data->save();

        $arr_internalwork = [];
        $arr_internalwork['department'] = $request->new_section;
        $arr_internalwork['immediate_supervisor'] = $request->immediate_supervisor ?? "N/A";
        $arr_internalwork['work_location'] = $request->new_location;
        $arr_internalwork['date_from'] = $request->date_of_effictivity;
        $arr_internalwork['employee_id'] = $data->employee_id;
        $arr_internalwork['position_title'] = $data->position_title;
        $arr_internalwork['employment_status'] = $data->employment_status;
        $arr_internalwork['actual_salary'] = $data->actual_salary;
        $arr_internalwork['hire_source'] = $data->hire_source;
        $arr_internalwork['salary_grades'] = $data->salary_grades;
        $arr_internalwork['status'] = EmployeeInternalWorkExperiencesStatus::CURRENT;
        $arr_internalwork['date_to'] = null;

        $transferData = InternalWorkExperience::create($arr_internalwork);

        if ($transferData) {
            return true;
        }

        return false;
    }

    public function promotionData($id, $request)
    {
        $data = InternalWorkExperience::where("id", "=", $id)->first();
        $data->status = EmployeeInternalWorkExperiencesStatus::PREVIOUS;
        $data->save();

        $arr_internalwork = [];
        $arr_internalwork['employee_id'] = $data->employee_id;
        $arr_internalwork['position_title'] = $request->designation_position;
        $arr_internalwork['employment_status'] = $request->new_employment_status;
        $arr_internalwork['department'] = $data->department;
        $arr_internalwork['immediate_supervisor'] = $data->immediate_supervisor ?? "N/A";
        $arr_internalwork['actual_salary'] = $request->salarygrade->monthly_salary_amount;
        $arr_internalwork['salary_grades'] = $request->salary_grades;
        $arr_internalwork['date_from'] = $request->date_from;
        $arr_internalwork['work_location'] = $data->work_location;
        $arr_internalwork['hire_source'] = $data->hire_source;
        $arr_internalwork['status'] = EmployeeInternalWorkExperiencesStatus::CURRENT;
        $arr_internalwork['date_to'] = null;

        $transferData = InternalWorkExperience::create($arr_internalwork);

        if ($transferData) {
            return true;
        }

        return false;
    }

    public function termination($id, $request)
    {
        $data = InternalWorkExperience::where("id", "=", $id)->first();
        $data->date_to = date("Y-m-d");
        $data->save();
        $arr = [];
        $arr['employee_id'] = $id;
        $arr['type_of_termination'] = $request->type_of_termination;
        $arr['reason_for_termination'] = $request->reasons_for_termination;
        $arr['eligible_for_rehire'] = $request->eligible_for_rehire;

        $transferData = Termination::create($arr);

        if ($transferData) {
            return true;
        }

        return false;
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $main = EmployeePersonnelActionNoticeRequest::find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
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
    public function update(UpdateEmployeePersonnelActionNoticeRequestRequest $request, $id)
    {
        $main = EmployeePersonnelActionNoticeRequest::find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
            $main->fill($request->validated());
            $main->approvals = json_encode($request->approvals);
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
        $main = EmployeePersonnelActionNoticeRequest::find($id);
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
