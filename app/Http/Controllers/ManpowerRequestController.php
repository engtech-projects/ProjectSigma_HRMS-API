<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Carbon;
use App\Models\ManpowerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\ManpowerServices;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ManpowerRequestResource;
use App\Http\Requests\StoreManpowerRequestRequest;
use App\Http\Requests\UpdateManpowerRequestRequest;
use App\Utils\PaginateResourceCollection;
use Exception;
use Illuminate\Http\Resources\Json\JsonResource;

class ManpowerRequestController extends Controller
{

    protected $manpowerServices;
    protected $manpowerRequestType = null;

    public function __construct(ManpowerServices $manpowerServices)
    {
        $this->manpowerRequestType = request()->get('type');
        $this->manpowerServices = $manpowerServices;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $manpowerRequests = $this->manpowerServices->getAll();
        $collection = collect(ManpowerRequestResource::collection($manpowerRequests));
        return new JsonResponse([
            'success' => true,
            'message' => 'Manpower Request fetched.',
            'data' => new JsonResource(PaginateResourceCollection::paginate($collection, 10))
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Show List Manpower requests that have status “For Hiring“ = Approve
     */
    public function get_hiring()
    {
        $main = ManpowerRequest::with('job_applicants')->where("request_status", '=', 'Approved')->get();
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }

    /**
     * Show View Complete details of Manpower Request with applicant || View Applicants list per Manpower Request
     */
    public function get_manpower_with_applicant()
    {
        $main = ManpowerRequest::with('job_applicants')->get();
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }

    public function get()
    {
        $id = Auth::user()->id;
        $main = ManpowerRequest::where("requested_by", '=', $id)->get();
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }

    /**
     * Show all requests to be approved/reviewed by current user
     */
    public function get_approve()
    {
        $manpowerRequests = $this->manpowerServices->getAllByAuthUser();
        return ManpowerRequestResource::collection($manpowerRequests);
    }

    public function approve_approval($request)
    {
        $id = Auth::user()->id;
        $main = ManpowerRequest::where([
            ["requested_by", '=', $id],
            ["id", '=', $request]
        ])->first();

        $newdata = json_decode('{}');
        $newdata->success = false;
        $newdata->message = "Failed approved.";

        if (!$main) {
            $newdata->message = "No data found.";
            return response()->json($newdata);
        }

        $approval = json_decode($main->approvals);
        foreach ($approval as $index => $key) {
            $data = json_decode($key);
            $type =  gettype($data);
            $approval_id = 0;
            $approval_status = "";

            if ($type == "object") {
                $approval_id = $data->user_id;
                $approval_status = $data->status;
            } elseif ($type == "array") {
                $approval_id = $data[$index]->user_id;
                $approval_status = $data[$index]->status;
            }

            if ($approval_status == "Denied") {
                break;
            }

            if ($approval_id != $id && $approval_status == "Pending") {
                break;
            }

            if ($approval_id == $id && $approval_status == "Pending") {
                if ($type == "object") {
                    $data->date_approved = Carbon::now();
                    $data->status = "Approved";
                } elseif ($type == "array") {
                    $data[$index]->date_approved = Carbon::now();
                    $data[$index]->status = "Approved";
                }
                $approval[$index] = json_encode($data);
                $newdata->success = true;
                $newdata->message = "Successfully approved.";
                break;
            }
        }

        if ($newdata->success) {
            $main->approvals = json_encode($approval);
            if ($main->save()) {
                $newdata->data = $main;
                return response()->json($newdata);
            }
        } else {
            $main->approvals = [];
        }

        $newdata->data = $main;
        return response()->json($newdata);
    }

    public function deny_approval($request)
    {
        $id = Auth::user()->id;
        $main = ManpowerRequest::where([
            ["requested_by", '=', $id],
            ["id", '=', $request]
        ])->first();

        $newdata = json_decode('{}');
        $newdata->success = false;
        $newdata->message = "Failed denied.";

        if (!$main) {
            $newdata->message = "No data found.";
            return response()->json($newdata);
        }

        $approval = json_decode($main->approvals);
        foreach ($approval as $index => $key) {
            $data = json_decode($key);
            $type =  gettype($data);
            $approval_id = 0;
            $approval_status = "";

            if ($type == "object") {
                $approval_id = $data->user_id;
                $approval_status = $data->status;
            } elseif ($type == "array") {
                $approval_id = $data[$index]->user_id;
                $approval_status = $data[$index]->status;
            }

            if ($approval_status == "Denied") {
                break;
            }

            if ($approval_id != $id && $approval_status == "Pending") {
                break;
            }

            if ($approval_id == $id && $approval_status == "Pending") {
                // $data->date_approved = Carbon::now();
                if ($type == "object") {
                    $data->status = "Denied";
                } elseif ($type == "array") {
                    $data[$index]->status = "Denied";
                }
                $approval[$index] = json_encode($data);
                $newdata->success = true;
                $newdata->message = "Successfully denied.";
                break;
            }
        }

        if ($newdata->success) {
            $main->approvals = json_encode($approval);
            if ($main->save()) {
                $newdata->data = $main;
                return response()->json($newdata);
            }
        } else {
            $main->approvals = [];
        }

        $newdata->data = $main;
        return response()->json($newdata);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreManpowerRequestRequest $request)
    {
        $main = new ManpowerRequest;
        $main->fill($request->validated());
        $data = json_decode('{}');
        $main->approvals = json_encode($request->approvals);
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
        $main = ManpowerRequest::find($id);
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
     * Show the form for editing the specified resource.
     */
    public function edit(ManpowerRequest $manpowerRequest)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateManpowerRequestRequest $request, ManpowerRequest $manpowerRequest)
    {
        try {
            $this->manpowerServices->update($request->validated(), $manpowerRequest);
        } catch (\Exception $e) {
            throw new Exception("Update transaction failed.");
        }

        return new JsonResponse([
            "success" => true, "message" => "Manpower request successfully approved."
        ]);



        /* $main = ManpowerRequest::find($id);
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
        return response()->json($data, 404); */
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $main = ManpowerRequest::find($id);
        $a = explode("/", $main->job_description_attachment);
        Storage::deleteDirectory("public/" . $a[0] . "/" . $a[1]);
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
    }
}
