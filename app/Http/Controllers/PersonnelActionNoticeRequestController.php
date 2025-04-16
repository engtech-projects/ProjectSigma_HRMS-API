<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use App\Http\Services\EmployeePanRequestService;
use App\Models\EmployeePanRequest;
use App\Http\Resources\EmployeePanRequestResource;
use App\Http\Requests\StoreEmployeePanRequestRequest;
use App\Http\Requests\UpdateEmployeePanRequestRequest;
use App\Models\CompanyEmployee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

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
    public function index(Request $request)
    {
        $panRequest = EmployeePanRequest::with(['employee', 'jobapplicantonly', 'department', 'salarygrade.salary_grade_level', 'position'])
            ->when($request->has("employee"), function ($query) use ($request) {
                $query->whereHas('employee', function ($q) use ($request) {
                    $q->where(DB::raw("CONCAT(first_name, ' ', middle_name, ' ', family_name)"), 'LIKE', "%{$request->employee}%");
                })->orWhereHas('jobapplicant', function ($q) use ($request) {
                    $q->where(DB::raw("CONCAT(firstname, ' ', middlename, ' ', lastname)"), 'LIKE', "%{$request->employee}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate();
        // TO FIX EMPLOYEE FILTER

        $paginated = EmployeePanRequestResource::collection($panRequest)->response()->getData(true);
        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetched.",
            "data" => $paginated
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeePanRequestRequest $request)
    {
        $valid = $request->validated();
        if (!$valid) {
            return new JsonResponse([
                "success" => false,
                "message" => "Create transaction failed."
                ], JsonResponse::HTTP_EXPECTATION_FAILED);
        }
        $data = $this->panRequestService->create($valid);
        $dataResource = new EmployeePanRequestResource($data);

        return new JsonResponse([
            "success" => true,
            "message" => "Successfully created.",
            "data" => $dataResource
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
        $paginated = EmployeePanRequestResource::collection($noticeRequest)->response()->getData(true);
        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetched.",
            "data" => $paginated
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
            'message' => 'Personnel Action Notice Request fetched.',
            'data' => EmployeePanRequestResource::collection($myApproval)->response()->getData(true)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $main = EmployeePanRequest::with(['employee', 'jobapplicantonly', 'department', 'salarygrade.salary_grade_level', 'position'])
        ->find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
            $data->message = "Successfully fetched.";
            $data->success = true;
            $data->data = new EmployeePanRequestResource($main);
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
        $maxHiring = EmployeePanRequest::addSelect(DB::raw("MAX(CAST(SUBSTRING(company_id_num, LOCATE('-', company_id_num, 6)+1, 4) AS UNSIGNED)) as companyid"))->first()->companyid;
        $max = $maxCompany > $maxHiring ? $maxCompany : $maxHiring;
        $date = Carbon::now()->format("ymj");
        return response()->json([
            "message" => "Success generate new Company ID.",
            "success" => true,
            "data" => "ECDC-" . $date . '-' . Str::padLeft($max + 1, 4, "0"),
        ]);
    }
}
