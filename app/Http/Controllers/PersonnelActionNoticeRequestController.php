<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Termination;
use App\Models\JobApplicants;
use Illuminate\Http\JsonResponse;
use App\Enums\EmployeeAddressType;
use App\Models\InternalWorkExperience;
use App\Enums\EmployeeRelatedPersonType;
use App\Utils\PaginateResourceCollection;
use App\Exceptions\TransactionFailedException;
use App\Enums\EmployeeCompanyEmploymentsStatus;
use App\Http\Services\EmployeePanRequestService;
use App\Enums\EmployeeInternalWorkExperiencesStatus;
use App\Models\EmployeePanRequest;
use App\Http\Resources\EmployeePanRequestResource;
use App\Http\Requests\StoreEmployeePanRequestRequest;
use App\Http\Requests\UpdateEmployeePanRequestRequest;
use App\Models\CompanyEmployee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PersonnelActionNoticeRequestController extends Controller
{
    protected $panRequestService;
    public function __construct(EmployeePanRequestService $panRequestService)
    {
        $this->panRequestService = $panRequestService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $panRequest = $this->panRequestService->getAll();
        $paginated = EmployeePanRequestResource::collection($panRequest);
        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetch.",
            "data" => PaginateResourceCollection::paginate(collect($paginated), 15)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeePanRequestRequest $request)
    {
        try {
            $valid = $request->validated();
            if(!$valid){
                return new JsonResponse([
                    "success" => false,
                    "message" => "Create transaction failed."
                    ], JsonResponse::HTTP_EXPECTATION_FAILED);
            }
            $this->panRequestService->create($valid);
        } catch (\Exception $e) {
            throw new TransactionFailedException("Create transaction failed.", 500, $e);
        }
        return new JsonResponse([
            "success" => true,
            "message" => "Successfully created."
        ], JsonResponse::HTTP_CREATED);
    }

    // can view all pan request made by logged in user
    public function myRequests()
    {
        $noticeRequest = $this->panRequestService->getMyRequests();
        if (empty($noticeRequest)) {
            return new JsonResponse([
                "success" => false,
                "message" => "No data found.",
            ]);
        }
        $paginated = EmployeePanRequestResource::collection($noticeRequest);
        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetch.",
            "data" => PaginateResourceCollection::paginate(collect($paginated), 15)
        ]);
    }

    /**
     * Show can view all pan request to be approved by logged in user (same login in manpower request)
     */
    public function myApprovals()
    {
        $myApproval = $this->panRequestService->getMyApprovals();
        if ($myApproval->isEmpty()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Personnel Action Notice Request fetch.',
            'data' => EmployeePanRequestResource::collection($myApproval)
        ]);
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
        $internalWork['employment_status'] = $request->employment_status;
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
            foreach ($main->children as $key) {
                $relatedChildren["name"] = $key->name;
                $relatedChildren["date_of_birth"] = $key->birthdate ?? null;
                $relatedChildren["type"] = EmployeeRelatedPersonType::CHILD;
                $employee->employee_related_person()->create($relatedChildren);
            }
        }

        // Employee Work Experience
        if (property_exists("workexperience", $main)) {
            foreach ($main->workexperience as $key) {
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
        $main = EmployeePanRequest::find($id);
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
    public function update(UpdateEmployeePanRequestRequest $request, $id)
    {
        $main = EmployeePanRequest::find($id);
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
        $main = EmployeePanRequest::find($id);
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

    public function generateIdNum()
    {
        // Locate to get the last index of - + 1 for start
        // max cast substring to get max number
        $maxCompany = CompanyEmployee::addSelect(DB::raw("MAX(CAST(SUBSTRING(employeedisplay_id, LOCATE('-', employeedisplay_id, 6)+1, 4) AS UNSIGNED)) as companyid"))->first()->companyid;
        $maxHiring = EmployeePanRequest::addSelect(DB::raw("MAX(CAST(SUBSTRING(company_id_num, 13, 4) AS UNSIGNED)) as companyid"))->first()->companyid;
        $max = $maxCompany > $maxHiring ? $maxCompany : $maxHiring;
        $date = Carbon::now()->format("ymj");
        return response()->json([
            "message" => "Success generate new Company ID.",
            "success" => true,
            "data" => "ECDC-" . $date . '-' . Str::padLeft($max + 1, 4, "0"),
        ]);
    }
}
