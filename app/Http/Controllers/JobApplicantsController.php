<?php

namespace App\Http\Controllers;

use App\Enums\JobApplicationStatusEnums;
use App\Enums\HiringStatuses;
use App\Models\JobApplicants;
use App\Models\ManpowerRequestJobApplicants;
use App\Http\Requests\JobApplicantRequest;
use App\Http\Requests\SearchEmployeeRequest;
use App\Http\Requests\StoreJobApplicantsRequest;
use App\Http\Requests\UpdateJobApplicantsRequest;
use App\Http\Requests\UpdateJobApplicantStatus;
use App\Http\Resources\AllJobApplicantResource;
use App\Http\Resources\JobApplicantResource;
use App\Utils\PaginateResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class JobApplicantsController extends Controller
{
    public const RADIR = "resume_attachment/";
    public const ALADIR = "application_letter_attachment/";

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $main = JobApplicants::with("manpower.position")->paginate();
        $collection = JobApplicantResource::collection($main)->response()->getData(true);

        return new JsonResponse([
            'success' => true,
            'message' => 'Job Applicant fetched.',
            'data' => $collection
        ]);
    }

    /**
     * Show For Hiring status
     * For Hiring Job Applicant
     */
    public function get_for_hiring(SearchEmployeeRequest $request)
    {
        $validatedData = $request->validated();
        $searchKey = $validatedData["key"];
        $main = JobApplicants::with("manpower")->select("id", "firstname", "middlename", "lastname")
            ->where(function ($q) use ($searchKey) {
                $q->orWhere('firstname', 'like', "%{$searchKey}%")
                    ->orWhere('firstname', 'like', "%{$searchKey}%")
                    ->orWhere(DB::raw("CONCAT(lastname, ', ', firstname, ', ', middlename)"), 'LIKE', $searchKey . "%")
                    ->orWhere(DB::raw("CONCAT(firstname, ', ', middlename, ', ', lastname)"), 'LIKE', $searchKey . "%");
            })
            ->whereHas('manpower', function ($query) {
                $query->where('manpower_request_job_applicants.hiring_status', JobApplicationStatusEnums::FOR_HIRING);
            })
            ->limit(25)
            ->orderBy('lastname')
            ->get()
            ->append(["fullname_first", "fullname_last"]);
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }

    public function getAllApplicant(JobApplicantRequest $request)
    {
        $valid = $request->validated();

        $main = JobApplicants::with("manpower.position")->where("status", JobApplicationStatusEnums::AVAILABLE->value);
        if (isset($valid["status"])) {
            $main = $main->whereHas('manpower', function ($query) use ($valid) {
                $query->where('manpower_request_job_applicants.hiring_status', $valid["status"]);
            });
        }

        if (isset($valid["name"])) {
            $searchKey = $valid["name"];
            $main = $main->where(function ($q) use ($searchKey) {
                $q->orWhere('firstname', 'like', "%{$searchKey}%")
                    ->orWhere('firstname', 'like', "%{$searchKey}%")
                    ->orWhere(DB::raw("CONCAT(lastname, ', ', firstname, ', ', middlename)"), 'LIKE', $searchKey . "%")
                    ->orWhere(DB::raw("CONCAT(firstname, ', ', middlename, ', ', lastname)"), 'LIKE', $searchKey . "%");
            });
        }

        if (isset($valid["paginated"])) {
            $main = $main->orderBy('lastname')->paginate();
            $collection = JobApplicantResource::collection($main)->response()->getData(true);

            return new JsonResponse([
                'success' => true,
                'message' => 'Job Applicant fetched.',
                'data' => $collection
            ]);
        } else {
            $main = $main->orderBy('lastname')->get();
            return new JsonResponse([
                'success' => true,
                'message' => 'Job Applicant fetched.',
                'data' => $main
            ]);
        }

    }

    /**
     * Show View Applicant Details
     */
    public function get()
    {
        $main = JobApplicants::with("manpower")->get();
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }

    public function updateApplicant(UpdateJobApplicantStatus $request, $id)
    {
        try {
            $valid = $request->validated();
            if ($valid) {
                DB::transaction(function() use ($valid, $id) {
                    $main = JobApplicants::find($id);
                    $record = ManpowerRequestJobApplicants::where('job_applicants_id', $main->id)
                    ->where('manpowerrequests_id', $valid["manpowerrequests_id"])->first();

                    $jobApplicantStatus = JobApplicationStatusEnums::AVAILABLE->value;
                    $manPowerRequestJobApplciantStatus = HiringStatuses::REJECTED->value;

                    if ($valid["status"] === HiringStatuses::FOR_HIRING->value) {
                        $jobApplicantStatus = JobApplicationStatusEnums::PROCESSING->value;
                        $manPowerRequestJobApplciantStatus = HiringStatuses::FOR_HIRING->value;
                    }

                    $valid["status"] = $jobApplicantStatus;
                    $record->hiring_status = $manPowerRequestJobApplciantStatus;
                    $record->processing_checklist = $valid["processing_checklist"];
                    $main->fill($valid);
                    $record->save();
                    $main->save();
                });
                return new JsonResponse([
                    "success" => true,
                    "message" => "Successfully save.",
                ], JsonResponse::HTTP_OK);
            }
        } catch (Exception $e) {
            return new JsonResponse([
                "success" => true,
                "message" => "Failed to save.",
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobApplicantsRequest $request)
    {
        $main = new JobApplicants();
        $validatedData = $request->validated();
        if (!$validatedData) {
            $data->message = "Save failed.";
            $data->success = false;
            return response()->json($data, 400);
        }

        $main->fill($validatedData);
        $data = json_decode('{}');

        $resume_attachment = $validatedData["resume_attachment"];
        $application_letter_attachmentfile = $validatedData['application_letter_attachment'];

        $hashmake = Hash::make('secret');
        $hashname = hash('sha256', $hashmake);

        $name1 = $resume_attachment->getClientOriginalName();
        $name2 = $application_letter_attachmentfile->getClientOriginalName();

        $resume_attachment->storePubliclyAs(JobApplicantsController::RADIR . $hashname, $name1, 'public');
        $application_letter_attachmentfile->storePubliclyAs(JobApplicantsController::ALADIR . $hashname, $name2, 'public');

        $main->resume_attachment = JobApplicantsController::RADIR . $hashname . "/" . $name1;
        $main->application_letter_attachment = JobApplicantsController::ALADIR . $hashname . "/" . $name2;
        $main->education = $validatedData['education'];
        $main->workexperience = $validatedData['workexperience'];
        $main->children = $validatedData['children'];
        $main->status = JobApplicationStatusEnums::PENDING;
        $main->date_of_application = Carbon::now();
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
        $main = JobApplicants::find($id);
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
    public function update(UpdateJobApplicantsRequest $request, $id)
    {
        $main = JobApplicants::find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
            $a1 = explode("/", $main->application_letter_attachment);
            $a2 = explode("/", $main->resume_attachment);

            $main->fill($request->validated());
            $hashmake = Hash::make('secret');
            $hashname = hash('sha256', $hashmake);
            if ($request->hasFile("application_letter_attachment")) {
                $check = JobApplicants::find($id);
                $file = $request->file('application_letter_attachment');
                $name = $file->getClientOriginalName();
                $file->storePubliclyAs(JobApplicantsController::ALADIR . $hashname, $name, 'public');
                Storage::deleteDirectory("public/" . $a1[0] . "/" . $a1[1]);
                $main->application_letter_attachment = JobApplicantsController::ALADIR . $hashname . "/" . $name;
            }

            if ($request->hasFile("resume_attachment")) {
                $check = JobApplicants::find($id);
                $file = $request->file('resume_attachment');
                $name = $file->getClientOriginalName();
                $file->storePubliclyAs(JobApplicantsController::RADIR . $hashname, $name, 'public');
                Storage::deleteDirectory("public/" . $a2[0] . "/" . $a2[1]);
                $main->resume_attachment = JobApplicantsController::RADIR . $hashname . "/" . $name;
            }

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
        $main = JobApplicants::find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
            $a = explode("/", $main->application_letter_attachment);
            if ($main->delete()) {
                Storage::deleteDirectory("public/" . JobApplicantsController::ALADIR . "/" . $a[0] . "/" . $a[1]);
                Storage::deleteDirectory("public/" . JobApplicantsController::RADIR . "/" . $a[0] . "/" . $a[1]);
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

    public function generatePDF(JobApplicants $application)
    {
        return view('reports.docs.application_form', ["application" => $application]);
    }
}
