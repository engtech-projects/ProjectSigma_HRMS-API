<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Termination;
use App\Models\JobApplicants;
use App\Models\ManpowerRequest;
use Illuminate\Http\JsonResponse;
use App\Enums\EmployeeAddressType;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreDisapprove;
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
        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetched.",
            "data" => EmployeePanRequestResource::collection($noticeRequest)
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
            'data' => EmployeePanRequestResource::collection($myApproval)
        ]);
    }

    public function failedMessage($newdata, $message)
    {
        $newdata->success = false;
        $newdata->message = $message;
        // $newdata->message = "Failed approved.";
        return response()->json($newdata);
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
}
