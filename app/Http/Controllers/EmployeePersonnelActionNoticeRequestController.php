<?php

namespace App\Http\Controllers;

use App\Enums\EmployeeAddressType;
use App\Enums\EmployeeRelatedPersonType;
use App\Models\EmployeePersonnelActionNoticeRequest;
use App\Http\Requests\StoreEmployeePersonnelActionNoticeRequestRequest;
use App\Http\Requests\UpdateEmployeePersonnelActionNoticeRequestRequest;
use App\Models\Employee;
use App\Models\InternalWorkExperience;
use App\Models\JobApplicants;
use App\Models\ManpowerRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EmployeePersonnelActionNoticeRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $main = EmployeePersonnelActionNoticeRequest::paginate(15);
        $data = json_decode('{}');
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
        $main = new EmployeePersonnelActionNoticeRequest();
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
        $main = EmployeePersonnelActionNoticeRequest::where("created_by", "=", $id)->get();
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
     * Show can view all pan request to be approved by logged in user (same login in manpower request)
     */
    public function getApprovals()
    {
        $id = Auth::user()->id;
        $main = EmployeePersonnelActionNoticeRequest::approval()
            ->whereJsonContains('approvals', ["user_id" => strval($id), "status" => "Pending"])->get();
        $newdata = json_decode('{}');
        $newdata->message = "Successfully fetch.";
        $newdata->success = true;
        $newdata->data = $main;
        return response()->json($newdata);
    }

    // logged in can approve pan request(if he is the current approval)
    public function approveApprovals($request)
    {
        // $request = pan_id
        $id = Auth::user()->id;
        $main = EmployeePersonnelActionNoticeRequest::where("id", $request)->with("jobapplicant")->first();
        $newdata = json_decode('{}');

        if (!$main) {
            $newdata->success = false;
            $newdata->message = "No data found.";
            return response()->json($newdata);
        }

        $this->hireApproved($main->pan_job_applicant_id);
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
            $newdata->message = "No data found.";
            return response()->json($newdata);
        }

        $count = count(json_decode($panreq->approvals));
        if ($next_approval == strval($id)) {
            $a = [];
            foreach (json_decode($panreq->approvals) as $key) {
                if ($key->user_id == strval($id) && $key->status == "Pending" && $approve == 0) {
                    $key->status = "Approved";
                    $key->date_approved = Carbon::now()->format('Y-m-d');
                    $approve = 1;
                    $count_approves += 1;
                }

                array_push($a, $key);
            }

            if ($count_approves >= $count) {
                // Approved All on Panreq
                if ($main->type == "New Hire") {
                    $saveData = $this->hireApproved($main->pan_job_applicant_id);
                    if ($saveData) {
                        $main->jobapplicant->status = "Hired";
                        JobApplicants::where("id", $main->pan_job_applicant_id)->update(["status" => "Hired"]);
                        ManpowerRequest::where("id", $main->jobapplicant->manpower->id)->update(["request_status" => "Approved"]);
                        $main->request_status = "Filled";
                    } else {
                        $newdata->success = false;
                        $newdata->message = "Failed approved.";
                        return response()->json($newdata);
                    }
                }

                if ($main->type == "Transfer") {
                    $this_internal = InternalWorkExperience::where("id", $main->employee_id)->first();
                    $this_internal->status = "inactive";
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

        $newdata->success = false;
        $newdata->message = "Failed approved.";
        return response()->json($newdata);
    }

    public function hireApproved($id)
    {
        $main = JobApplicants::where('id', '=', $id)->first();
        $employee = new Employee();
        $arr = [];
        $arr_workexperience = [];
        $arr_related = [];

        if (!$main) {
            return false;
        }
        // Employee
        $arr["family_name"] = $main->lastname;
        $arr["first_name"] = $main->firstname;
        $arr["middle_name"] = $main->middlename;
        $arr["gender"] = $main->gender;
        $arr["date_of_birth"] = $main->date_of_birth;
        $arr["place_of_birth"] = $main->place_of_birth;
        $arr["date_of_marriage"] = $main->date_of_marriage;
        $arr["citizenship"] = $main->citizenship;
        $arr["blood_type"] = $main->blood_type;
        $arr["civil_status"] = $main->civil_status;
        $arr["mobile_number"] = $main->contact_info;
        $arr["email"] = $main->email;
        $arr["religion"] = $main->religion;
        $arr["weight"] = $main->weight;
        $arr["height"] = $main->height;
        $employee->fill($arr)->save();

        // Employee Address
        $arr["employee_id"] = $employee->id;
        $arr["street"] = $main->pre_address_street;
        $arr["brgy"] = $main->pre_address_brgy;
        $arr["city"] = $main->pre_address_city;
        $arr["zip"] = $main->pre_address_zip;
        $arr["province"] = $main->pre_address_province;
        $arr["type"] = EmployeeAddressType::PRESENT;
        $employee->employee_address()->create($arr);
        $arr["street"] = $main->per_address_street;
        $arr["brgy"] = $main->per_address_brgy;
        $arr["city"] = $main->per_address_city;
        $arr["zip"] = $main->per_address_zip;
        $arr["province"] = $main->per_address_province;
        $arr["type"] = EmployeeAddressType::PERMANENT;
        $employee->employee_address()->create($arr);

        // Employee Spouse
        if (property_exists("name_of_spouse", $main)) {
            $arr_related["name"] = $main->name_of_spouse;
            $arr_related["date_of_birth"] = $main->date_of_birth_spouse ?? null;
            $arr_related["contact_no"] = $main->telephone_spouse ?? null;
            $arr_related["occupation"] = $main->occupation_spouse ?? null;
            $arr_related["type"] = EmployeeRelatedPersonType::SPOUSE;
            $employee->employee_related_person()->create($arr_related);
        }

        // Employee Father
        if (property_exists("father_name", $main)) {
            $arr_related["name"] = $main->father_name;
            $arr_related["type"] = EmployeeRelatedPersonType::FATHER;
            $employee->employee_related_person()->create($arr_related);
        }

        // Employee Mother
        if (property_exists("mother_name", $main)) {
            $arr_related["name"] = $main->mother_name;
            $arr_related["type"] = EmployeeRelatedPersonType::MOTHER;
            $employee->employee_related_person()->create($arr_related);
        }

        // Employee In Case of Emergency
        if (property_exists("icoe_name", $main)) {
            $arr_related["name"] = $main->icoe_name ?? null;
            $arr_related["street"] = $main->icoe_street ?? null;
            $arr_related["brgy"] = $main->icoe_brgy ?? null;
            $arr_related["city"] = $main->icoe_city ?? null;
            $arr_related["zip"] = $main->icoe_zip ?? null;
            $arr_related["province"] = $main->icoe_province ?? null;
            $arr_related["relationship"] = $main->icoe_relationship ?? null;
            $arr_related["contact_no"] = $main->telephone_icoe ?? null;
            $arr_related["type"] = EmployeeRelatedPersonType::CONTACT_PERSON;
            $employee->employee_related_person()->create($arr_related);
        }

        // Employee Children
        if (property_exists("children", $main)) {
            foreach (json_decode($main->children) as $key) {
                $arr_related["name"] = $key->name;
                $arr_related["date_of_birth"] = $key->birthdate ?? null;
                $arr_related["type"] = EmployeeRelatedPersonType::CHILD;
                $employee->employee_related_person()->create($arr_related);
            }
        }

        // Employee Work Experience
        if (property_exists("workexperience", $main)) {
            foreach (json_decode($main->workexperience) as $key) {
                $arr_workexperience["date_from"] = $key->inclusive_dates_from ?? null;
                $arr_workexperience["date_to"] = $key->inclusive_dates_to ?? null;
                $arr_workexperience["position_title"] = $key->position_title ?? null;
                $arr_workexperience["company_name"] = $key->dpt_agency_office_company ?? null;
                $arr_workexperience["salary"] = $key->monthly_salary ?? null;
                $arr_workexperience["status_of_appointment"] = $key->status_of_appointment ?? null;
                $employee->employee_externalwork()->create($arr_related);
            }
        }
        return true;
        // Employee Education
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
