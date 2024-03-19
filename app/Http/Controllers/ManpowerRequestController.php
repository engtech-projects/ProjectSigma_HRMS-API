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
     * Show List Manpower requests that have status “For Hiring“ = Approve
     */
    public function forHiring()
    {
        $main = ManpowerRequest::with('job_applicants')->where("request_status", '=', 'Approved')->get();
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }


    public function myRequest()
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
    public function myApproval()
    {
        $testRequest = ManpowerRequest::first();
        return $testRequest;
        $manpowerRequests = $this->manpowerServices->getAllManpowerRequest();
        return ManpowerRequestResource::collection($manpowerRequests);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreManpowerRequestRequest $request)
    {
        $main = new ManpowerRequest;
        $attributes = $request->validated();
        $main->fill($attributes);
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
